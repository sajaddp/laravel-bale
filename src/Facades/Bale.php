<?php

declare(strict_types=1);

namespace Sajaddp\Bale\Facades;

use Illuminate\Support\Facades\Facade;
use Sajaddp\Bale\BaleClient;

/**
 * @method static array<mixed> getMe()
 * @method static array<mixed> sendMessage(int|string $chatId, string $text, array<string, mixed> $options = [])
 * @method static bool setWebhook(string $url)
 * @method static bool deleteWebhook()
 * @method static array<mixed> getWebhookInfo()
 * @method static array<mixed> forwardMessage(int|string $chatId, int|string $fromChatId, int $messageId)
 * @method static array<mixed> copyMessage(int|string $chatId, int|string $fromChatId, int $messageId)
 * @method static bool sendChatAction(int|string $chatId, string $action)
 * @method static bool answerCallbackQuery(string $callbackQueryId, array<string, mixed> $options = [])
 * @method static array<mixed> editMessageText(int|string $chatId, int $messageId, string $text, array<string, mixed> $options = [])
 * @method static array<mixed> editMessageCaption(int|string $chatId, int $messageId, array<string, mixed> $options = [])
 * @method static array<mixed> editMessageReplyMarkup(int|string $chatId, int $messageId, array<string, mixed> $options = [])
 * @method static bool deleteMessage(int|string $chatId, int $messageId)
 */
class Bale extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaleClient::class;
    }
}
