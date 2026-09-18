# Laravel Bale

A minimal Laravel 13 package for calling the Bale Bot API.

## Requirements

- Laravel 13
- PHP 8.3 or newer

This repository is not currently published on Packagist.

## Phase 01 features

- `Bale::getMe()`
- `Bale::sendMessage()`

No other Bale endpoints are included in this phase.

## Configuration

Set your Bale bot token in the application's environment:

```dotenv
BALE_BOT_TOKEN=your-bot-token
```

The package is discovered automatically by Laravel. To publish the configuration file, use:

```bash
php artisan vendor:publish --tag=bale-config
```

## Usage

Import the package facade explicitly:

```php
use Sajaddp\Bale\Facades\Bale;
```

Fetch the current bot:

```php
$bot = Bale::getMe();
```

Send a message. Any Bale-supported optional `sendMessage` values can be passed through `options`; `chatId` and `text` cannot be overridden by options.

```php
$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
    options: [
        'reply_to_message_id' => 123,
    ],
);
```

Both methods return Bale's `result` payload as an array.

## Errors

Bale API failures throw `Sajaddp\Bale\Exceptions\BaleRequestException`. The exception exposes `baleErrorCode`, `description`, and `parameters`.

```php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
```

## Laravel Boost

The package ships a concise Boost guideline and a `bale-development` skill. After installing Laravel Boost in a consuming application, run:

```bash
php artisan boost:install
```

Boost will include this package's guideline and, when skills are selected, install the bundled `bale-development` skill.

## Development

```bash
composer install
composer test
```
