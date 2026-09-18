---
name: bale-development
description: Implement or modify Bale Bot API integrations with the laravel-bale package.
---

# Bale Development

Use this skill when working on a Bale Bot API integration.

Configure BALE_BOT_TOKEN, import Sajaddp\Bale\Facades\Bale, and use only the package API that exists. Do not infer Telegram-only methods, fields, or behavior.

~~~php
use Sajaddp\Bale\Facades\Bale;

$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
    options: ['reply_to_message_id' => 123],
);
~~~

Required arguments always win over conflicting option keys. Pass only Bale-documented optional values through options.

## Webhook configuration

Configure outgoing delivery with Bale::setWebhook('https://example.com/bale/updates'), inspect it with Bale::getWebhookInfo(), and remove it with Bale::deleteWebhook().

This package does not provide a webhook router, controller, middleware, or incoming route. Receive Bale JSON updates through normal Laravel routing and controllers.

## Callbacks and actions

~~~php
Bale::answerCallbackQuery(
    callbackQueryId: $update['callback_query']['id'],
    options: ['text' => 'انجام شد', 'show_alert' => true],
);

Bale::sendChatAction(chatId: 123456789, action: 'upload_photo');
~~~

Answer every callback query to clear the inline button waiting state. If its identifier starts with 1, the user is on an older Bale client that does not support callback feedback; implement any application fallback yourself.

## Message operations

~~~php
Bale::forwardMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::copyMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);

Bale::editMessageText(chatId: 123456789, messageId: 42, text: 'متن جدید');
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

setWebhook, deleteWebhook, sendChatAction, answerCallbackQuery, and deleteMessage return booleans. Documented Bale result objects are arrays. Bale does not document a result type for message-editing methods, so they return Bale's raw result.

Valid Bale `ok: false` API responses throw BaleRequestException. Other unsuccessful HTTP responses that are not valid Bale error envelopes use Laravel HTTP client failure semantics and throw Illuminate\Http\Client\RequestException. Test application code with Laravel's HTTP fake rather than real requests.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~
