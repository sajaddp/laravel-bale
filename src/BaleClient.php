<?php

declare(strict_types=1);

namespace Sajaddp\Bale;

use Illuminate\Support\Facades\Http;
use LogicException;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use UnexpectedValueException;

class BaleClient
{
    /** @return array<mixed> */
    public function getMe(): array
    {
        return $this->request('getMe');
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<mixed>
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return $this->request('sendMessage', array_merge($options, [
            'chat_id' => $chatId,
            'text' => $text,
        ]));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<mixed>
     */
    private function request(string $method, array $data = []): array
    {
        $response = Http::acceptJson()
            ->asJson()
            ->post($this->urlFor($method), $data);

        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('ok', $payload)) {
            $response->throw();

            throw new UnexpectedValueException('Bale returned an invalid API response.');
        }

        if ($payload['ok'] !== true) {
            throw BaleRequestException::fromResponse($payload);
        }

        $result = $payload['result'] ?? null;

        if (! is_array($result)) {
            throw new UnexpectedValueException('Bale returned a successful response without an array result.');
        }

        return $result;
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
