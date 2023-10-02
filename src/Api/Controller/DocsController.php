<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Application\UI\TemplateFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Finder;
use SplFileInfo;

/**
 * @Api\Path("/docs")
 */
final class DocsController extends AbstractController
{
    public function __construct(
        private readonly bool $enabled,
        private readonly string $schemaDir,
        private readonly TemplateFactory $templateFactory,
    ) {}

    /**
     * @Api\Path("/")
     * @Api\Method("GET")
     */
    public function swagger(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $this->checkDocsEnabled();

        $template = $this->templateFactory->createTemplate();
        assert($template instanceof DefaultTemplate);
        $uri = $request->getUri();
        $endpoints = [];

        assert($template instanceof Template);

        foreach (Finder::findFiles('schema.*.json')->in($this->schemaDir) as $file) {
            assert($file instanceof SplFileInfo);

            preg_match('/schema\.(v[\d\.]+)\.json/', $file->getFilename(), $matches);

            if (!isset($matches[1])) {
                continue;
            }

            $endpoints[] = [
                'url' => rtrim($uri->getPath(), '/') . '/schema/' . $matches[1],
                'name' => $matches[1],
            ];
        }

        $template->setFile(__DIR__ . '/templates/swagger-ui.latte');
        $template->add('endpoints', $endpoints);

        return $response->writeBody($template->renderToString())
            ->withHeader('Content-Type', 'text/html');
    }

    /**
     * @Api\Path("/schema/{version}")
     * @Api\Method("GET")
     * @Api\RequestParameters({
     *      @Api\RequestParameter(name="version", type="string", in="path", required=true, description="OpenApi schema version")
     * })
     */
    public function schema(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        $this->checkDocsEnabled();

        $version = $request->getParameter('version');
        $filename = sprintf(
            '%s/schema.%s.json',
            rtrim($this->schemaDir, DIRECTORY_SEPARATOR),
            $version,
        );

        if (!is_file($filename)) {
            throw new ClientErrorException('Schema not found.', ApiResponse::S404_NOT_FOUND);
        }

        $schema = @file_get_contents($filename);

        if (false === $schema) {
            throw new ServerErrorException('Schema is not readable.', ApiResponse::S500_INTERNAL_SERVER_ERROR);
        }

        return $response->writeBody($schema)
            ->withHeader('Content-Type', 'application/json');
    }

    private function checkDocsEnabled(): void
    {
        if (!$this->enabled) {
            throw new ClientErrorException('Api documentation is disabled.', ApiResponse::S400_BAD_REQUEST);
        }
    }
}
