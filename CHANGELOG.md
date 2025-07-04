# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## 1.2.1 - 2025-06-23

### Added
- Added command `make install-assets.watch`.

### Changed
- Frontend assets are now built using the `assets-builder` container. There is no need to make any changes when using the `make install-assets` command.

### Fixed
- Fixed handling of requests with invalid actions in OAuth presenter (fixed inside the package `68publishers/oauth`).

## 1.2.0 - 2025-06-10

### Added
- Added `<meta name="robots" content="noindex,nofollow">` to all pages to prevent pages indexing.

### Changed
- Updated to PHP 8.4

## 1.1.0 - 2025-05-13

### Added
- Added optional ENV variable `CONSENT_GRID_ESTIMATE_ONLY`. If the variable has the value `1` then the DataGrid only displays the estimated number of records and has a simplified pagination (only the " previous " and " next " buttons). Enabling this variable solves the performance problem when displaying the DataGrid if there are already too many records in the database.
- Added nginx reverse proxy with SSL for local and dev environments.

### Removed
- Removed ENV variable `GRID_COUNT_LIMIT`. The limit is now always `100 000` when the `CONSENT_GRID_ESTIMATE_ONLY` variable is disabled.

### Changed
- Changed minimum PHP version to `8.3`.
- Changed `68publishers/php` Docker images to the latest versions.
- Updated composer dependencies and codebase syntax (typed constants, readonly classes etc.).
- Email notifications are not sent for disabled projects.
- Updated development guide and installation instructions.

### Fixed
- Fixed broken styles in notification emails.

## 1.0.0 - 2024-03-06

### Added

- Added optional ENV variables `REDIS_DB_CACHE` (default `0`) and `REDIS_DB_SESSIONS` (default `1`).
- Added the `Development Guide` and `Product Documentation`.

### Changed

- Updated the README.

## 0.12.0 - 2023-02-27

### Added

- Added authentication via Azure AD.
- Added new categories `ad_user_data` and `ad_personalization` in the fixtures.

### Fixed

- Fixed resolving of cookie suggestions that were not already created by crawler.

## 0.11.0 - 2023-10-24

### Added

- Added ENV variable `GRID_COUNT_LIMIT`.
- Added environment integration. In addition to the "Default environment", other custom environments can be defined in the application settings. These environments can be assigned to projects and cookies and can be filtered on them in the consent list or on the dashboard.
- Added optional query parameter `environment` in the Cookies API.
- Added optional body parameter `environment` in the Consent API.
- Added new OpenApi schema `v1.1.0`.

### Changed

- The maximum number of entries in the consent list is limited to value of the ENV variable `GRID_COUNT_LIMIT` due to performance issues (default 100.000).

## 0.10.0 - 2023-08-31

### Added
- Added monthly statistics command `bin/console cmp:monthly-statistics <project-code> [--accepted-all] [--rejected-all] [--by-categories <categories>] [--unique] [--year <year>] [--format <format>]`.

### Changed
- Changed `68publishers/php` Docker images to the latest versions.

### Fixed
- Fixed number formatting based on user's locale preferences.
- Fixed OpenApi schema loading on the URL `/api/docs`.

## 0.9.0 - 2023-08-10

### Added
- Added build of production Docker images.
- Added integration with `68publishers/crawler`:
  - Crawler configuration in the global settings.
  - Management for scenarios and scenario schedulers
- Added cookie suggestions based on Crawler's results.
- Added numbers of cookie suggestions in Dashboard statistics and Weekly Overview mail notification.
- Added new mail notification for cookie suggestions.

### Changed
- Updated PHP version to `8.1`.
- Changed base Docker images to the latest `68publishers/php`.
- Updated OpenApi schema.
- Updated coding style (new Php-Cs-Fixer configuration, PHP 8 features).

### Fixed
- Fixed performance of the list of consents.

## 0.8.0 - 2023-04-26

### Added
- Added ability to create cookies with duplicate name but different category in the context of the same provider
- Added ability to define cookie expiration with minutes (i modifier) or seconds (s modifier)
- Added delete action for cookie lists on pages Admin:Cookies, Admin:EditProvider and Admin:Project:Cookies

### Changed
- Changed behaviour of switch buttons on enter keydown. Enter submits associated form.
- Improved numbers formatting in the application and mails + updated footer links
