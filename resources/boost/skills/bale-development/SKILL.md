---
name: bale-development
description: Implement or modify Bale Bot API integrations with the laravel-bale package.
---

# Bale Development

Use this skill when working on a Bale Bot API integration.

Configure the bot token in the application's environment:

```dotenv
BALE_BOT_TOKEN=your-bot-token
```

Import the explicit facade and use only the package API that exists:

```php
use Sajaddp\Bale\Facades\Bale;

$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
    options: [
        'reply_to_message_id' => 123,
    ],
);
```

Pass only Bale-supported optional `sendMessage` values in `options`; `chatId` and `text` always take precedence over conflicting option keys.

Handle Bale API failures explicitly:

```php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
```

Test application code with Laravel's HTTP fake rather than real requests. Do not infer unsupported Bale APIs from Telegram compatibility.
