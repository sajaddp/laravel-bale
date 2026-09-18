# Laravel Bale

A minimal Laravel 13 package for calling the Bale Bot API.

## Requirements

- Laravel 13
- PHP 8.3 or newer

## Features

- Basic messaging with getMe and sendMessage
- Webhook configuration and status
- Message forwarding, copying, editing, and deletion
- Chat actions and callback-query responses

## Configuration

Set your Bale bot token in the application's environment:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

The package is discovered automatically by Laravel. To publish the configuration file, use:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

Import the facade explicitly:

~~~php
use Sajaddp\Bale\Facades\Bale;
~~~

## Basic messaging

~~~php
$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
    options: [
        'reply_to_message_id' => 123,
    ],
);
~~~

Required arguments always take precedence over conflicting keys in options.

## Webhooks

~~~php
Bale::setWebhook('https://example.com/bale/updates');

$webhook = Bale::getWebhookInfo();

Bale::deleteWebhook();
~~~

This package configures Bale's outgoing webhook only. It does not register an incoming route or provide a webhook framework; receive Bale's JSON updates through your application's normal Laravel routes and controllers.

## Callbacks

~~~php
Bale::answerCallbackQuery(
    callbackQueryId: $update['callback_query']['id'],
    options: [
        'text' => 'انجام شد',
        'show_alert' => true,
    ],
);
~~~

Call this method even when no response text is needed, so the inline button can leave its waiting state. Callback queries whose identifier starts with 1 come from older Bale clients that do not support this feedback; application-level fallback behavior is your responsibility.

## Message operations

~~~php
$forwarded = Bale::forwardMessage(
    chatId: '@target_channel',
    fromChatId: 123456789,
    messageId: 42,
);

$copied = Bale::copyMessage(
    chatId: '@target_channel',
    fromChatId: 123456789,
    messageId: 42,
);

Bale::sendChatAction(chatId: 123456789, action: 'upload_photo');

$edited = Bale::editMessageText(
    chatId: 123456789,
    messageId: 42,
    text: 'متن جدید',
);

Bale::editMessageCaption(
    chatId: 123456789,
    messageId: 42,
    options: ['caption' => 'زیرنویس جدید'],
);

Bale::editMessageReplyMarkup(
    chatId: 123456789,
    messageId: 42,
    options: ['reply_markup' => $replyMarkup],
);

Bale::deleteMessage(chatId: 123456789, messageId: 42);
~~~

Use only Bale-documented optional values in options. Bale result objects are returned as arrays; setWebhook, deleteWebhook, sendChatAction, answerCallbackQuery, and deleteMessage return booleans.

## Errors

Bale API failures throw Sajaddp\Bale\Exceptions\BaleRequestException. The exception exposes baleErrorCode, description, and parameters.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

## Laravel Boost

The package ships a concise Boost guideline and a bale-development skill. After installing Laravel Boost in a consuming application, run:

~~~shell
php artisan boost:install
~~~

Boost will include this package's guideline and, when skills are selected, install the bundled bale-development skill.

## Development

~~~shell
composer install
composer test
~~~
