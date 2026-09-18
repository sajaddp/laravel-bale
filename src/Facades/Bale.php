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
 * @method static mixed editMessageText(int|string $chatId, int $messageId, string $text, array<string, mixed> $options = [])
 * @method static mixed editMessageCaption(int|string $chatId, int $messageId, array<string, mixed> $options = [])
 * @method static mixed editMessageReplyMarkup(int|string $chatId, int $messageId, array<string, mixed> $options = [])
 * @method static bool deleteMessage(int|string $chatId, int $messageId)
 * @method static array<mixed> getUpdates(array<string, mixed> $options = [])
 * @method static array<mixed> sendPhoto(int|string $chatId, int|string $fromChatId, string|\SplFileInfo $photo, array<string, mixed> $options = [])
 * @method static array<mixed> sendAudio(int|string $chatId, string|\SplFileInfo $audio, array<string, mixed> $options = [])
 * @method static array<mixed> sendDocument(int|string $chatId, string|\SplFileInfo $document, array<string, mixed> $options = [])
 * @method static array<mixed> sendVideo(int|string $chatId, string|\SplFileInfo $video, array<string, mixed> $options = [])
 * @method static array<mixed> sendAnimation(int|string $chatId, string|\SplFileInfo $animation, array<string, mixed> $options = [])
 * @method static array<mixed> sendVoice(int|string $chatId, string|\SplFileInfo $voice, array<string, mixed> $options = [])
 * @method static array<mixed> sendMediaGroup(int|string $chatId, array<int, array<string, mixed>> $media, array<string, mixed> $options = [], array<array-key, \SplFileInfo> $attachments = [])
 * @method static array<mixed> sendLocation(int|string $chatId, float $latitude, float $longitude, array<string, mixed> $options = [])
 * @method static array<mixed> sendContact(int|string $chatId, int|string $phoneNumber, string $firstName, array<string, mixed> $options = [])
 * @method static array<mixed> getFile(string $fileId)
 */
class Bale extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaleClient::class;
    }
}
