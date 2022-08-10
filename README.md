# Consent Management Platform

> 🛡 Manage user consents and cookie widget with easy.

## Table of Contents
* [About CMP](#about-cmp)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Installation](#installation)
* [Product Documentation](#product-documentation)
* [Development Guide](#development-guide)
* [Known Issues](#known-issues)

## About CMP
...

## Getting Started

### Prerequisites
- Docker
- Make

### Installation
```sh
$ git clone <repository-url> cmp
$ cd cmp
$ cp .env.dist .env
$ make init
```

Visit http://localhost:8888 and sign in via `admin@68publishers.io` / `admin` credentials.

See [Makefile](./Makefile) for other usefull commands.

## Product Documentation
...

## Development Guide
...

## Known issues
When running application stack locally and get the following message:
```sh
An exception occurred in the driver:
SQLSTATE[08006] [7] could not translate host name "cmp-db" to address: Name does not resolve
```

Simply restart the `db` service manualy:
```sh
$ docker compose restart db
```
