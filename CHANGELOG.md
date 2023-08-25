# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

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
