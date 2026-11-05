#!/usr/bin/env bash
# Local replacement of the GH Actions workflow (.github/workflows/build.yml), run it while connected to the HPT VPN.
# Builds a clean checkout of CHECKOUT from GitHub (like actions/checkout), not the working directory.
# Requires `docker login registry2.hptronic.cz` and `docker login` (DockerHub) beforehand.
# Usage: VERSION=1.2.3 CHECKOUT=v1.2.3 [PLATFORMS=linux/amd64,linux/arm64] [PUSH_HPT=1] [PUSH_DOCKERHUB=1] ./docker/build/release.sh
set -euo pipefail
cd "$(dirname "$0")/../.."

VERSION="${VERSION:?VERSION is required (e.g. VERSION=1.2.3)}"
VERSION="${VERSION#v}"
CHECKOUT="${CHECKOUT:?CHECKOUT is required (branch or tag, e.g. CHECKOUT=v1.2.3)}"
PLATFORMS="${PLATFORMS:-linux/amd64}"
PUSH_HPT="${PUSH_HPT:-1}"
PUSH_DOCKERHUB="${PUSH_DOCKERHUB:-1}"
REPOSITORY="https://github.com/68publishers/consent-management-platform"
IMAGE_HPT_APP="${IMAGE_HPT_APP:-registry2.hptronic.cz/dev/cmp/cmp}"
IMAGE_HPT_WORKER="${IMAGE_HPT_WORKER:-registry2.hptronic.cz/dev/cmp/cmp/worker}"
IMAGE_DOCKERHUB="${IMAGE_DOCKERHUB:-68publishers/cmp}"

if ! [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+(-[0-9A-Za-z.-]+)?$ ]]; then
    echo "Error: VERSION must be in semver format (e.g. VERSION=1.2.3)." >&2
    exit 1
fi

# <target> <image>:<tag>, the same names as GH Actions
builds=()
if [[ "$PUSH_DOCKERHUB" == 1 ]]; then
    builds+=("app $IMAGE_DOCKERHUB:app-$VERSION" "worker $IMAGE_DOCKERHUB:worker-$VERSION")
fi
if [[ "$PUSH_HPT" == 1 ]]; then
    builds+=("app $IMAGE_HPT_APP:$VERSION" "worker $IMAGE_HPT_WORKER:$VERSION")
fi
if [[ ${#builds[@]} -eq 0 ]]; then
    echo "Error: At least one of the options PUSH_HPT or PUSH_DOCKERHUB must be enabled." >&2
    exit 1
fi

context=$(mktemp -d)
metadata=$(mktemp)
trap 'rm -rf "$context" "$metadata"' EXIT
echo "cloning $REPOSITORY ($CHECKOUT) ..."
git clone -q --depth 1 --branch "$CHECKOUT" "$REPOSITORY.git" "$context"

if docker buildx inspect multi_arch_builder > /dev/null 2>&1; then
    echo "builder multi_arch_builder does exist, skipping initialization."
else
    echo "builder multi_arch_builder does not exist, initialization starting..."
    docker buildx create --name multi_arch_builder --driver docker-container --bootstrap
fi

# same OCI labels as docker/metadata-action@v4 generates in GH Actions
labels=(
    --label "org.opencontainers.image.title=consent-management-platform"
    --label "org.opencontainers.image.description=🛡 Easily manage user consents and the cookie widget"
    --label "org.opencontainers.image.url=$REPOSITORY"
    --label "org.opencontainers.image.source=$REPOSITORY"
    --label "org.opencontainers.image.created=$(date -u +%Y-%m-%dT%H:%M:%S.000Z)"
    --label "org.opencontainers.image.revision=$(git -C "$context" rev-parse HEAD)"
    --label "org.opencontainers.image.licenses=NOASSERTION"
)

# same as GH Actions: push by digest, then create the tagged manifest list (OCI index)
for build in "${builds[@]}"; do
    target="${build%% *}"
    ref="${build#* }"
    image="${ref%:*}"
    tag="${ref##*:}"
    echo "building [$PLATFORMS] image $ref (target $target) ..."
    docker buildx build \
        -f "$context/docker/build/Dockerfile" \
        --pull \
        --builder multi_arch_builder \
        --platform "$PLATFORMS" \
        --target "$target" \
        --provenance=false \
        "${labels[@]}" \
        --label "org.opencontainers.image.version=$tag" \
        --output "type=image,name=$image,push-by-digest=true,name-canonical=true,push=true,oci-mediatypes=true" \
        --metadata-file "$metadata" \
        "$context"
    digest=$(jq -r '."containerimage.digest"' "$metadata")
    docker buildx imagetools create -t "$ref" "$image@$digest"
done
echo "done"
