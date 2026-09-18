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
- One-request long-polling updates
- Media, location, contact, and file-metadata APIs

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

## Updates

~~~php
$updates = Bale::getUpdates([
    'offset' => $nextOffset,
    'limit' => 100,
    'timeout' => 30,
]);
~~~

`getUpdates` makes exactly one Bale request. When its Bale `timeout` option is an integer, the HTTP transport timeout has five seconds of headroom (and is never below 30 seconds). Applications using long polling are responsible for advancing and persisting their own offset.

## Media and files

For media inputs, a string is passed to Bale unchanged and represents a Bale `file_id` or an HTTP URL. A `SplFileInfo` is an explicit local-file upload; strings are never inspected as local paths.

~~~php
use SplFileInfo;

$fromBale = Bale::sendDocument(
    chatId: 123456789,
    document: 'bale-file-id',
    options: ['caption' => 'فایل قبلی'],
);

$fromUrl = Bale::sendVideo(
    chatId: 123456789,
    video: 'https://example.test/video.mp4',
);

$uploaded = Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/example.pdf')),
    options: ['caption' => 'فایل جدید'],
);
~~~

The supported single-media methods are `sendPhoto`, `sendAudio`, `sendDocument`, `sendVideo`, `sendAnimation`, and `sendVoice`. Bale's current `sendPhoto` documentation also requires `from_chat_id`:

~~~php
$photo = Bale::sendPhoto(
    chatId: '@target_channel',
    fromChatId: '@source_channel',
    photo: new SplFileInfo(storage_path('app/example.jpg')),
);
~~~

For media groups, use Bale's documented media array directly. When local files are needed, explicitly reference multipart fields with `attach://` and provide matching `SplFileInfo` attachments:

~~~php
$messages = Bale::sendMediaGroup(
    chatId: 123456789,
    media: [
        ['type' => 'photo', 'media' => 'attach://first'],
        ['type' => 'photo', 'media' => 'attach://second'],
    ],
    attachments: [
        'first' => new SplFileInfo(storage_path('app/first.jpg')),
        'second' => new SplFileInfo(storage_path('app/second.jpg')),
    ],
);
~~~

String media sources and media groups without local attachments use JSON requests. Local `SplFileInfo` uploads and explicit media-group attachments use multipart/form-data. The package JSON-serializes Bale's structured multipart fields such as `reply_markup` and `media`.

## Location, contacts, and file metadata

~~~php
$location = Bale::sendLocation(
    chatId: 123456789,
    latitude: 35.6892,
    longitude: 51.3890,
    options: ['horizontal_accuracy' => 12.5],
);

$contact = Bale::sendContact(
    chatId: 123456789,
    phoneNumber: '+989120000000',
    firstName: 'سجاد',
    options: ['last_name' => 'دهشیری'],
);

$file = Bale::getFile('bale-file-id');
~~~

These methods return Bale result objects as arrays. `getFile` returns metadata only; this package does not provide a file-download helper.

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

Use only Bale-documented optional values in options. Documented Bale result objects are returned as arrays; setWebhook, deleteWebhook, sendChatAction, answerCallbackQuery, and deleteMessage return booleans. Bale does not document the result type of message-editing methods, so they return Bale's raw result.

## Errors

Valid Bale `ok: false` API responses throw Sajaddp\Bale\Exceptions\BaleRequestException. The exception exposes baleErrorCode, description, and parameters. Other unsuccessful HTTP responses that are not valid Bale error envelopes use Laravel HTTP client failure semantics and throw Illuminate\Http\Client\RequestException.

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
