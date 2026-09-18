<?php

declare(strict_types=1);

namespace Sajaddp\Bale;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Assert;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use SplFileInfo;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use UnexpectedValueException;

class BaleClient
{
    private const LONG_POLL_HEADROOM_SECONDS = 5;

    private const GUZZLE_MILLISECONDS_PER_SECOND = 1000;

    /** @return array<string, mixed> */
    public function getMe(): array
    {
        return $this->requestArray('getMe');
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return $this->requestArray('sendMessage', array_merge($options, [
            'chat_id' => $chatId,
            'text' => $text,
        ]));
    }

    /**
     * Reply to a raw Bale Message array using its documented chat and message identifiers.
     *
     * @param  array<string, mixed>  $message
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function replyToMessage(array $message, string $text, array $options = []): array
    {
        $messageId = $message['message_id'] ?? null;
        $chat = $message['chat'] ?? null;
        $chatId = is_array($chat) ? ($chat['id'] ?? null) : null;

        if (! is_int($messageId) || ! is_int($chatId)) {
            throw new InvalidArgumentException('Bale message must contain an integer message_id and integer chat.id.');
        }

        return $this->sendMessage($chatId, $text, array_merge($options, [
            'reply_to_message_id' => $messageId,
        ]));
    }

    public function setWebhook(string $url): bool
    {
        return $this->requestBoolean('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook(): bool
    {
        return $this->requestBoolean('deleteWebhook');
    }

    /** @return array<string, mixed> */
    public function getWebhookInfo(): array
    {
        return $this->requestArray('getWebhookInfo');
    }

    /** @return array<string, mixed> */
    public function forwardMessage(int|string $chatId, int|string $fromChatId, int $messageId): array
    {
        return $this->requestArray('forwardMessage', [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ]);
    }

    /** @return array<string, mixed> */
    public function copyMessage(int|string $chatId, int|string $fromChatId, int $messageId): array
    {
        return $this->requestArray('copyMessage', [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ]);
    }

    public function sendChatAction(int|string $chatId, string $action): bool
    {
        return $this->requestBoolean('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    /** @param array<string, mixed> $options */
    public function answerCallbackQuery(string $callbackQueryId, array $options = []): bool
    {
        return $this->requestBoolean('answerCallbackQuery', array_merge($options, [
            'callback_query_id' => $callbackQueryId,
        ]));
    }

    /**
     * Ask a user to submit or edit a review for this Bale bot after a delay.
     */
    public function askReview(int $userId, int $delaySeconds): bool
    {
        return $this->requestBoolean('askReview', [
            'user_id' => $userId,
            'delay_seconds' => $delaySeconds,
        ]);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function editMessageText(int|string $chatId, int $messageId, string $text, array $options = []): mixed
    {
        return $this->request('editMessageText', array_merge($options, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
        ]));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function editMessageCaption(int|string $chatId, int $messageId, array $options = []): mixed
    {
        return $this->request('editMessageCaption', array_merge($options, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function editMessageReplyMarkup(int|string $chatId, int $messageId, array $options = []): mixed
    {
        return $this->request('editMessageReplyMarkup', array_merge($options, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]));
    }

    public function deleteMessage(int|string $chatId, int $messageId): bool
    {
        return $this->requestBoolean('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Make one getUpdates request. Applications are responsible for advancing offsets.
     *
     * @param  array<string, mixed>  $options
     * @return array<int, array<string, mixed>>
     */
    public function getUpdates(array $options = []): array
    {
        $transportTimeout = is_int($options['timeout'] ?? null)
            ? $this->transportTimeoutForLongPoll($options['timeout'])
            : null;

        return $this->requestArray('getUpdates', $options, $transportTimeout);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendPhoto(int|string $chatId, int|string $fromChatId, string|SplFileInfo $photo, array $options = []): array
    {
        return $this->sendMedia('sendPhoto', $chatId, 'photo', $photo, $options, [
            'from_chat_id' => $fromChatId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendAudio(int|string $chatId, string|SplFileInfo $audio, array $options = []): array
    {
        return $this->sendMedia('sendAudio', $chatId, 'audio', $audio, $options);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendDocument(int|string $chatId, string|SplFileInfo $document, array $options = []): array
    {
        return $this->sendMedia('sendDocument', $chatId, 'document', $document, $options);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendVideo(int|string $chatId, string|SplFileInfo $video, array $options = []): array
    {
        return $this->sendMedia('sendVideo', $chatId, 'video', $video, $options);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendAnimation(int|string $chatId, string|SplFileInfo $animation, array $options = []): array
    {
        return $this->sendMedia('sendAnimation', $chatId, 'animation', $animation, $options);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendVoice(int|string $chatId, string|SplFileInfo $voice, array $options = []): array
    {
        return $this->sendMedia('sendVoice', $chatId, 'voice', $voice, $options);
    }

    /**
     * @param  list<array<string, mixed>>  $media
     * @param  array<string, mixed>  $options
     * @param  array<array-key, SplFileInfo>  $attachments
     * @return array<int, array<string, mixed>>
     */
    public function sendMediaGroup(int|string $chatId, array $media, array $options = [], array $attachments = []): array
    {
        $this->assertMediaAttachmentsExist($media, $this->attachmentNames($attachments));

        $data = array_merge($options, [
            'chat_id' => $chatId,
            'media' => $media,
        ]);

        if ($attachments === []) {
            return $this->requestArray('sendMediaGroup', $data);
        }

        return $this->requestMultipartArray('sendMediaGroup', $data, $attachments, ['media']);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendLocation(int|string $chatId, float $latitude, float $longitude, array $options = []): array
    {
        return $this->requestArray('sendLocation', array_merge($options, [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]));
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function sendContact(int|string $chatId, int|string $phoneNumber, string $firstName, array $options = []): array
    {
        return $this->requestArray('sendContact', array_merge($options, [
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
        ]));
    }

    /** @return array<string, mixed> */
    public function getFile(string $fileId): array
    {
        return $this->requestArray('getFile', ['file_id' => $fileId]);
    }

    /**
     * Download a Bale file's binary body after retrieving its metadata.
     */
    public function downloadFile(string $fileId): string
    {
        $file = $this->getFile($fileId);
        $filePath = $file['file_path'] ?? null;

        if (! is_string($filePath) || $filePath === '') {
            throw new UnexpectedValueException('Bale returned file metadata without a usable file_path.');
        }

        return Http::get($this->downloadUrlFor($filePath))->throw()->body();
    }

    /**
     * Fake only this package's Bale API and file-download requests.
     *
     * The real client still builds requests and parses Bale response envelopes.
     */
    public function fake(): void
    {
        $botUrlPrefix = sprintf('https://tapi.bale.ai/bot%s/', $this->token());
        $downloadUrlPrefix = sprintf('https://tapi.bale.ai/file/bot%s/', $this->token());

        Http::fake(function (Request $request) use ($botUrlPrefix, $downloadUrlPrefix): ?PromiseInterface {
            if (str_starts_with($request->url(), $downloadUrlPrefix)) {
                return Http::response('Bale fake file download');
            }

            if (! str_starts_with($request->url(), $botUrlPrefix)) {
                return null;
            }

            return $this->fakeResponseFor(substr($request->url(), strlen($botUrlPrefix)));
        });
    }

    /**
     * @param  array<string, mixed>|Closure(Request): bool|null  $condition
     */
    public function assertSent(string $method, array|Closure|null $condition = null): void
    {
        Assert::assertNotEmpty(
            $this->recordedFor($method, $condition),
            sprintf('Expected Bale method [%s] to be sent.', $method),
        );
    }

    public function assertSentTimes(string $method, int $times): void
    {
        Assert::assertCount(
            $times,
            $this->recordedFor($method),
            sprintf('Expected Bale method [%s] to be sent %d times.', $method, $times),
        );
    }

    /**
     * @param  array<string, mixed>|Closure(Request): bool|null  $condition
     */
    public function assertNotSent(string $method, array|Closure|null $condition = null): void
    {
        Assert::assertEmpty(
            $this->recordedFor($method, $condition),
            sprintf('Bale method [%s] was sent unexpectedly.', $method),
        );
    }

    public function assertNothingSent(): void
    {
        Assert::assertEmpty(
            Http::recorded(fn (Request $request): bool => $this->isBalePackageRequest($request)),
            'Bale package HTTP requests were sent unexpectedly.',
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function request(string $method, array $data = [], int|float|null $transportTimeout = null): mixed
    {
        $request = Http::acceptJson()->asJson();

        if ($transportTimeout !== null) {
            $request = $request->timeout($transportTimeout);
        }

        $response = $request->post($this->urlFor($method), $data);

        return $this->parseResponse($response);
    }

    private function fakeResponseFor(string $method): ?PromiseInterface
    {
        if (in_array($method, [
            'setWebhook',
            'deleteWebhook',
            'sendChatAction',
            'answerCallbackQuery',
            'deleteMessage',
            'askReview',
        ], true)) {
            return Http::response(['ok' => true, 'result' => true]);
        }

        if (in_array($method, [
            'editMessageText',
            'editMessageCaption',
            'editMessageReplyMarkup',
        ], true)) {
            return Http::response(['ok' => true, 'result' => true]);
        }

        if ($method === 'getFile') {
            return Http::response([
                'ok' => true,
                'result' => [
                    'file_id' => 'bale-fake-file',
                    'file_unique_id' => 'bale-fake-file',
                    'file_size' => 0,
                    'file_path' => 'bale-fake/file',
                ],
            ]);
        }

        if (in_array($method, [
            'getMe',
            'sendMessage',
            'getWebhookInfo',
            'forwardMessage',
            'copyMessage',
            'getUpdates',
            'sendPhoto',
            'sendAudio',
            'sendDocument',
            'sendVideo',
            'sendAnimation',
            'sendVoice',
            'sendMediaGroup',
            'sendLocation',
            'sendContact',
        ], true)) {
            return Http::response(['ok' => true, 'result' => []]);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|Closure(Request): bool|null  $condition
     * @return Collection<int, array{0: Request, 1: Response|null}>
     */
    private function recordedFor(string $method, array|Closure|null $condition = null): Collection
    {
        return Http::recorded(function (Request $request) use ($method, $condition): bool {
            if ($request->url() !== $this->urlFor($method)) {
                return false;
            }

            if (is_array($condition)) {
                return $this->payloadContains($request->data(), $condition);
            }

            return $condition === null || $condition($request);
        });
    }

    /**
     * @param  array<mixed>  $actual
     * @param  array<mixed>  $expected
     */
    private function payloadContains(array $actual, array $expected): bool
    {
        foreach ($expected as $key => $value) {
            if (! array_key_exists($key, $actual)) {
                return false;
            }

            if (is_array($value)) {
                if (! is_array($actual[$key]) || ! $this->payloadContains($actual[$key], $value)) {
                    return false;
                }

                continue;
            }

            if ($actual[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    private function isBalePackageRequest(Request $request): bool
    {
        return str_starts_with($request->url(), sprintf('https://tapi.bale.ai/bot%s/', $this->token()))
            || str_starts_with($request->url(), sprintf('https://tapi.bale.ai/file/bot%s/', $this->token()));
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<array-key, SplFileInfo>  $files
     * @param  array<int, string>  $jsonFields
     */
    private function requestMultipart(string $method, array $data, array $files, array $jsonFields = []): mixed
    {
        /** @var array<string, string> $paths */
        $paths = [];

        foreach ($files as $name => $file) {
            $paths[(string) $name] = $this->readablePath($file);
        }

        $request = Http::acceptJson();

        foreach ($files as $name => $file) {
            $name = (string) $name;
            $handle = fopen($paths[$name], 'rb');

            if ($handle === false) {
                throw new InvalidArgumentException(sprintf('Bale upload file "%s" cannot be opened for reading.', $paths[$name]));
            }

            $request = $request->attach($name, $handle, $this->filenameFor($file));
        }

        $response = $request->post($this->urlFor($method), $this->multipartData($data, $jsonFields));

        return $this->parseResponse($response);
    }

    private function parseResponse(Response $response): mixed
    {
        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('ok', $payload) || ! is_bool($payload['ok'])) {
            $response->throw();

            throw new UnexpectedValueException('Bale returned an invalid API response.');
        }

        if ($payload['ok'] === false) {
            if (! $this->isValidErrorEnvelope($payload, $response)) {
                $response->throw();

                throw new UnexpectedValueException('Bale returned an invalid API error response.');
            }

            throw BaleRequestException::fromResponse($payload);
        }

        $response->throw();

        if (! array_key_exists('result', $payload)) {
            throw new UnexpectedValueException('Bale returned a successful response without a result.');
        }

        return $payload['result'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<mixed>
     */
    private function requestArray(string $method, array $data = [], int|float|null $transportTimeout = null): array
    {
        $result = $this->request($method, $data, $transportTimeout);

        if (! is_array($result)) {
            throw new UnexpectedValueException('Bale returned a successful response without an array result.');
        }

        return $result;
    }

    /** @param array<string, mixed> $data */
    private function requestBoolean(string $method, array $data = []): bool
    {
        $result = $this->request($method, $data);

        if (! is_bool($result)) {
            throw new UnexpectedValueException('Bale returned a successful response without a boolean result.');
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $options
     * @param  array<string, int|string>  $required
     * @return array<string, mixed>
     */
    private function sendMedia(string $method, int|string $chatId, string $field, string|SplFileInfo $file, array $options, array $required = []): array
    {
        $data = array_merge($options, $required, [
            'chat_id' => $chatId,
            $field => $file,
        ]);

        if (is_string($file)) {
            return $this->requestArray($method, $data);
        }

        unset($data[$field]);

        return $this->requestMultipartArray($method, $data, [$field => $file], ['reply_markup']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<array-key, SplFileInfo>  $files
     * @param  array<int, string>  $jsonFields
     * @return array<mixed>
     */
    private function requestMultipartArray(string $method, array $data, array $files, array $jsonFields = []): array
    {
        $result = $this->requestMultipart($method, $data, $files, $jsonFields);

        if (! is_array($result)) {
            throw new UnexpectedValueException('Bale returned a successful response without an array result.');
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $jsonFields
     * @return array<string, mixed>
     */
    private function multipartData(array $data, array $jsonFields): array
    {
        foreach ($jsonFields as $field) {
            if (array_key_exists($field, $data) && ! is_string($data[$field])) {
                $data[$field] = json_encode($data[$field], JSON_THROW_ON_ERROR);
            }
        }

        return $data;
    }

    private function transportTimeoutForLongPoll(int $baleTimeout): int
    {
        // Guzzle converts the seconds value to float before multiplying by 1000.
        // One second below the integer division boundary remains representable.
        $largestSafeGuzzleTimeout = intdiv(PHP_INT_MAX, self::GUZZLE_MILLISECONDS_PER_SECOND) - 1;
        $largestBaleTimeoutWithHeadroom = $largestSafeGuzzleTimeout - self::LONG_POLL_HEADROOM_SECONDS;

        if ($baleTimeout > $largestBaleTimeoutWithHeadroom) {
            return 0;
        }

        return max(30, $baleTimeout + self::LONG_POLL_HEADROOM_SECONDS);
    }

    /**
     * @param  array<int, array<string, mixed>>  $media
     * @param  array<string, true>  $attachmentNames
     */
    private function assertMediaAttachmentsExist(array $media, array $attachmentNames): void
    {
        foreach ($this->mediaAttachmentReferences($media) as $name) {
            if (! array_key_exists($name, $attachmentNames)) {
                throw new InvalidArgumentException(sprintf('Bale media attachment "%s" does not have a matching local file.', $name));
            }
        }
    }

    /**
     * @param  array<int, mixed>  $media
     * @return array<int, string>
     */
    private function mediaAttachmentReferences(array $media): array
    {
        $references = [];

        foreach ($media as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (isset($item['media']) && is_string($item['media'])) {
                $this->addAttachmentReference($references, $item['media']);
            }

            if (in_array($item['type'] ?? null, ['audio', 'document', 'video'], true)
                && isset($item['thumbnail'])
                && is_string($item['thumbnail'])) {
                $this->addAttachmentReference($references, $item['thumbnail']);
            }
        }

        return $references;
    }

    /** @param array<int, string> $references */
    private function addAttachmentReference(array &$references, string $value): void
    {
        if (str_starts_with($value, 'attach://')) {
            $references[] = substr($value, strlen('attach://'));
        }
    }

    /**
     * @param  array<array-key, SplFileInfo>  $attachments
     * @return array<string, true>
     */
    private function attachmentNames(array $attachments): array
    {
        $names = [];

        foreach (array_keys($attachments) as $name) {
            $names[(string) $name] = true;
        }

        return $names;
    }

    private function readablePath(SplFileInfo $file): string
    {
        $path = $file->getPathname();

        if (! $file->isFile() || ! $file->isReadable()) {
            throw new InvalidArgumentException(sprintf('Bale upload file "%s" does not exist or is not readable.', $path));
        }

        return $path;
    }

    private function filenameFor(SplFileInfo $file): string
    {
        if ($file instanceof UploadedFile && $file->getClientOriginalName() !== '') {
            return $file->getClientOriginalName();
        }

        return $file->getFilename();
    }

    /** @param array<mixed> $payload */
    private function isValidErrorEnvelope(array $payload, Response $response): bool
    {
        if (array_key_exists('parameters', $payload)) {
            $rawPayload = json_decode($response->body());

            if (! is_array($payload['parameters'])
                || ! $rawPayload instanceof stdClass
                || ! property_exists($rawPayload, 'parameters')
                || ! $rawPayload->parameters instanceof stdClass) {
                return false;
            }

            if (array_key_exists('retry_after', $payload['parameters']) && ! is_int($payload['parameters']['retry_after'])) {
                return false;
            }
        }

        return is_int($payload['error_code'] ?? null)
            && (! array_key_exists('description', $payload) || is_string($payload['description']));
    }

    private function urlFor(string $method): string
    {
        return sprintf('https://tapi.bale.ai/bot%s/%s', $this->token(), $method);
    }

    private function downloadUrlFor(string $filePath): string
    {
        return sprintf('https://tapi.bale.ai/file/bot%s/%s', $this->token(), $filePath);
    }

    private function token(): string
    {
        $token = config('bale.token');

        if (! is_string($token) || trim($token) === '') {
            throw new LogicException('Bale bot token is not configured. Set BALE_BOT_TOKEN before making Bale API requests.');
        }

        return $token;
    }
}
