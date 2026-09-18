<?php

declare(strict_types=1);

namespace Sajaddp\Bale;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use LogicException;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use stdClass;
use UnexpectedValueException;

class BaleClient
{
    /** @return array<mixed> */
    public function getMe(): array
    {
        return $this->requestArray('getMe');
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<mixed>
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return $this->requestArray('sendMessage', array_merge($options, [
            'chat_id' => $chatId,
            'text' => $text,
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

    /** @return array<mixed> */
    public function getWebhookInfo(): array
    {
        return $this->requestArray('getWebhookInfo');
    }

    /** @return array<mixed> */
    public function forwardMessage(int|string $chatId, int|string $fromChatId, int $messageId): array
    {
        return $this->requestArray('forwardMessage', [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ]);
    }

    /** @return array<mixed> */
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
     * @param  array<string, mixed>  $data
     */
    private function request(string $method, array $data = []): mixed
    {
        $response = Http::acceptJson()
            ->asJson()
            ->post($this->urlFor($method), $data);

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
    private function requestArray(string $method, array $data = []): array
    {
        $result = $this->request($method, $data);

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

    private function token(): string
    {
        $token = config('bale.token');

        if (! is_string($token) || trim($token) === '') {
            throw new LogicException('Bale bot token is not configured. Set BALE_BOT_TOKEN before making Bale API requests.');
        }

        return $token;
    }
}
