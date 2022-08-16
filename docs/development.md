<div align="center" style="text-align: center; margin-bottom: 50px">
<img src="images/logo.svg" alt="Consent Management Platform logo" align="center" width="150">
<h1 align="center">Development Guide</h1>
</div>

<br>

## Table of Contents
* [Stack](#stack)
* [ENV variables](#env-variables)
* [Frontend](#frontend)
* [Fixtures](#fixtures)
* [Sending emails](#sending-emails)
* [Notification commands setup](#notification-commands-setup)
* [API and integration](#api-and-integration)
* [How to update to the latest version](#how-to-update-to-the-latest-version)
* [Contributing](#contributing)

## Stack

The application runs in Docker and consists of [three services](../docker-compose.yml):

1) App - Nginx webserver with PHP `7.4`, composer and yarn
2) Db - PostgreSQL in the version `13.6`
3) Redis - Redis in the version `5.0.8`

The container names are `cmp-app`, `cmp-db` and `cmp-redis`.
You can connect to each of the containers using the following command

```sh
$ docker exec -it <CONTAINER_NAME> sh 
```

The migration to PHP 8.x is of course planned soon, but for the time being we had to stick with version 7.4 for internal reasons.

## ENV Variables

The application needs several correctly set ENV variables to run correctly.
In the default installation, the variables are set for the application to work, but for production use, some of them need to be set/re-set

| Variable name               | Type               | Default                     | Additional info                                                                                                  |
|-----------------------------|--------------------|-----------------------------|------------------------------------------------------------------------------------------------------------------|
| APP_DEBUG                   | Boolean            | 1                           | Forces dev/debug mode                                                                                            |
| APP_DEBUG_COOKIE_SECRET     | String             | hey_tracy                   | The dev/debug mode can be enabled with cookie. A key is `debug_please` and a value is equal to this ENV variable |
| COOKIE_SECURE               | Boolean            | 0                           | True for sending a secure flag (https)                                                                           |
| PROJECT_URL                 | String             | http://localhost:8888       | Full URL to the document root of the application                                                                 |
| TRUSTED_PROXIES             | CommaSeparatedList | -                           | Optional, IP or IPs separated by a comma. The range you can enter like 1.0.0.0/1                                 |
| RECAPTCHA_ENABLED           | Boolean            | 0                           | Enables/Disables recaptcha in the application (forgot password)                                                  |
| GOOGLE_RECAPTCHA_SITE_KEY   | String             | -                           | The site key for Google Recaptcha v3                                                                             |
| GOOGLE_RECAPTCHA_SECRET_KEY | String             | -                           | The secret key for Google Recaptcha                                                                              |
| DB_HOST                     | String             | 127.0.0.1                   | -                                                                                                                |
| DB_PORT                     | Int                | 5432                        | -                                                                                                                |
| DB_NAME                     | String             | cmp                         | -                                                                                                                |
| DB_USER                     | String             | root                        | -                                                                                                                |
| DB_PASSWORD                 | String             | root                        | -                                                                                                                |
| REDIS_HOST                  | String             | 127.0. 0.1                  | -                                                                                                                |
| REDIS_PORT                  | Int                | 6379                        | -                                                                                                                |
| REDIS_AUTH                  | String             | redis_pass                  | Password, optional                                                                                               |
| SMTP_ENABLED                | Boolean            | 0                           | -                                                                                                                |
| SMTP_HOST                   | String             | -                           | -                                                                                                                |
| SMTP_PORT                   | Int                | -                           | -                                                                                                                |
| SMTP_USERNAME               | String             | -                           | -                                                                                                                |
| SMTP_PASSWORD               | String             | -                           | -                                                                                                                |
| SMTP_SECURE                 | String             | -                           | `ssl` or `tls`                                                                                                   |
| MAILER_EMAIL                | String             | info@cmp.io                 | Email address for email sending                                                                                  |
| MAILER_FROM                 | String             | Consent Management Platform | Name for email address for email sending                                                                         |
| SENTRY_DSN                  | String             | https://hash@sentry.io/123  | -                                                                                                                |
| GTM_CONTAINER_ID            | String             | -                           | Optional, GTM will be initialized only if the variable is passed.                                                |
| API_DOCS_ENABLED            | Boolean            | 1                           | Optional, enables OpenApi schema and Swagger UI.                                                                 |

All ENV variables are also listed in the [.env.dist](../.env.dist) file.

## Frontend

As far as the visual side is concerned, the application is based mainly on Tailwind CSS and Alpine.js components.
The final build is created using Webpack encore and PostCSS.

You can rebuild the assets with following command:

```sh
$ make install-assets
```

The command internally runs `yarn install` and webpack encore in the production mode.

Alternatively, the build can be run manually directly inside the container:

```sh
$ docker exec -it cmp-app sh
$ yarn run encore prod # or `dev` 
```

## Fixtures

Default demo data is automatically imported when the application is [installed](../README.md#installation).
If you want to restart the data, you can do id by running the following command:

```sh
$ make data
```

Alternatively:

```sh
$ docker exec -it cmp-app sh
$ bin/console doctrine:fixtures:load
```

:exclamation: Do not run fixtures on production!

## Sending emails

By default, emails are sent using the PHP function `mail()`. However, the `sendmail` extension is not installed in the container and therefore emails are not sent.
For this reason, you need to configure the SMPT server through the ENV variables.
For example, Google's SMTP server configuration would look something like this:

```sh
SMTP_ENABLED=1
SMTP_HOST=smtp.gmail.com
SMTP_PORT=465
SMTP_USERNAME=your email address
SMTP_PASSWORD=your token
SMTP_SECURE=ssl
```

If the debug mode is enabled (`APP_DEBUG=1` or the user has set the `APP_DEBUG_COOKIE_SECRET` cookie in the browser), the emails are ALWAYS saved to local files and can be viewed in the Tracy Bar

<img src="images/tracy-bar-mails.png" alt="Emails in the Tracy Bar" width="400">

## Notification commands setup

There are two console commands that send notifications to users.

### Weekly overview

The command sends weekly report with consent statistics for all projects.

```sh
$ bin/console cmp:weekly-overview
```

In your CRONTAB, schedule the command to run once a week, e.g. every Monday at 4:00 a.m.

### Consent decrease notification

The command sends notifications about consent decrease for the previous day for all projects that are related to a notified user.

```sh
$ bin/console cmp:consent-decrease-notifier
```

In your CRONTAB, schedule the command to run once a day, e.g. every day at 4:00 a.m.

## API and integration

The application exposes our API to communicate with the cookie widget.

All endpoints are described using OpenApi. To display the Swagger UI, set the ENV variable `API_DOCS_ENABLED` to `1` and open the `http://localhost:8888/api/docs` page in your browser.

If you use [68publishers/cookie-consent](https://github.com/68publishers/cookie-consent) on your sites, there is no need to manually integrate or call anything, everything is already prepared, and you just need to set the GTM tag correctly.
You can read how to integrate it in the 68publishers/cookie-consent [documentation](https://github.com/68publishers/cookie-consent#integration-with-cmp-application).

## How to update to the latest version

:exclamation: When updating, please always read the notes on newly released versions carefully!

Usually there are several things to do, such as:

- run composer install
- run yarn install
- run webpack encore
- run database migrations
- clear cache
- ...

All these tasks are included in the `make install` command, so in most cases it should be enough to download the latest version of the application and run:

```sh
$ make install
```

## Contributing

If you find any problems or have a specific question, please create an issue on GitHub. We will address it as soon as possible.
Please try to describe the issue as best you can (under what circumstances the issue arises, error messages, etc.).

If you want to contribute to the code (ideally after the previous discussion in an issue), create a fork of the repository with its own branch and then create a pull request with it.

Follow the uniform coding standards and coding style.

Before committing and creating a pull request, run the following command in the application container:

```sh
$ vendor/bin/php-cs-fixer fix -v
```
