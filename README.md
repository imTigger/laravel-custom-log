# Laravel CustomLog
Laravel failsafe custom logging library

- Log to multiple destinations
- Log to File
- Log to MySQL
- Log to Redis
- Log to syslog (Local/Remote)
- Log to Graylog (TCP/UDP)
- (Optional) Failsafe (Don't throw any exceptions in case logger fails)
- (Optional) Replace Laravel log (Laravel <= 5.5)
- (Optional) Register as Laravel logger channel (Laravel >= 5.6)

## Installation

`composer require imtigger/laravel-custom-log`

On Laravel 5.4 and below, add the ServiceProvider to your `config/app.php`

`Imtigger\LaravelCustomLog\LaravelCustomLogServiceProvider::class`

Publish Config

`php artisan vendor:publish --provider="Imtigger\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=config`

Publish MySQL Migration (Optional, for Log to MySQL)

`php artisan vendor:publish --provider="Imtigger\\LaravelCustomLog\\LaravelCustomLogServiceProvider" --tag=migration`

## Choose Log Destinations

Add config into `.env`, you may enable multiple destinations

### File

- CUSTOM_LOG_TEXT_ENABLE (true|false, default=true)

### MySQL

- CUSTOM_LOG_MYSQL_ENABLE (true|false, default=false)
- DB_LOG_CONNECTION (connection defined in database.php, default=mysql)
- DB_LOG_TABLE (default=logs)

### Redis

- CUSTOM_LOG_REDIS_ENABLE (true|false, default=false)
- REDIS_LOG_CONNECTION (connection defined in cache.php, default=default)
- REDIS_LOG_KEY

### Syslog

- CUSTOM_LOG_SYSLOG_ENABLE (true|false, default=false)
- CUSTOM_LOG_SYSLOG_HOST
- CUSTOM_LOG_SYSLOG_PORT (default=514)

### Gelf (Graylog)

- CUSTOM_LOG_GELF_ENABLE (true|false, default=false)
- CUSTOM_LOG_GELF_PROTOCOL (TCP|UDP, default=UDP)
- CUSTOM_LOG_GELF_HOST
- CUSTOM_LOG_GELF_PORT (default=12201)

## Basic Usage

`CustomLog::emergency($channel, $message, $context)`

`CustomLog::alert($channel, $message, $context)`

`CustomLog::critical($channel, $message, $context)`

`CustomLog::error($channel, $message, $context)`

`CustomLog::warning($channel, $message, $context)`

`CustomLog::notice($channel, $message, $context)`

`CustomLog::info($channel, $message, $context)`

`CustomLog::debug($channel, $message, $context)`

`CustomLog::log($level, $channel, $message, $context)`

## Replace Laravel log (Laravel <= 5.5)

Edit your `bootstrap/app.php`, add this before returning the application

```
$app->configureMonologUsing(function ($monolog) {
    $monolog->pushHandler(Imtigger\LaravelCustomLog\CustomLog::getSystemHandler());
});
```
## Register as Laravel logger channel (Laravel >= 5.6)

Edit your `config/logging.php`, add this to the `channels` array

```
'customlog' => [
    'driver' => 'custom',
    'via' => Imtigger\LaravelCustomLog\CustomLog::class,
]
```
