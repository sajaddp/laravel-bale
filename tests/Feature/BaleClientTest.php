<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sajaddp\Bale\BaleClient;
use Sajaddp\Bale\BaleServiceProvider;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use Sajaddp\Bale\Facades\Bale;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

it('makes package configuration available and publishes it with the bale-config tag', function (): void {
    expect(config('bale.token'))->toBe('test-token')
        ->and(BaleServiceProvider::pathsToPublish(BaleServiceProvider::class, 'bale-config'))
        ->toContain(config_path('bale.php'));
});

it('registers a singleton Bale client in the container', function (): void {
    expect(app(BaleClient::class))
        ->toBeInstanceOf(BaleClient::class)
        ->toBe(app(BaleClient::class));
});

it('resolves the facade to the registered Bale client', function (): void {
    expect(Bale::getFacadeRoot())->toBe(app(BaleClient::class));
});

it('sends a getMe request and returns only Bale result payload', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => true,
            'result' => ['id' => 42, 'username' => 'phase_one_bot'],
        ]),
    ]);

    expect(Bale::getMe())->toBe(['id' => 42, 'username' => 'phase_one_bot']);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getMe'
            && $request->method() === 'POST'
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->data() === [];
    });
});

it('sends required sendMessage values and Bale-supported options', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMessage' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 99],
        ]),
    ]);

    expect(Bale::sendMessage(
        chatId: 123456789,
        text: 'سلام',
        options: [
            'parse_mode' => 'MarkdownV2',
            'reply_to_message_id' => 12,
        ],
    ))->toBe(['message_id' => 99]);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/sendMessage'
            && $request->method() === 'POST'
            && $request->data() === [
                'parse_mode' => 'MarkdownV2',
                'reply_to_message_id' => 12,
                'chat_id' => 123456789,
                'text' => 'سلام',
            ];
    });
});

it('does not allow options to override required sendMessage values', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMessage' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 100],
        ]),
    ]);

    Bale::sendMessage(
        chatId: 123456789,
        text: 'سلام',
        options: ['chat_id' => 1, 'text' => 'نباید ارسال شود'],
    );

    Http::assertSent(function (Request $request): bool {
        return $request->data() === [
            'chat_id' => 123456789,
            'text' => 'سلام',
        ];
    });
});

it('throws an inspectable exception for Bale API failures', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => false,
            'error_code' => 429,
            'description' => 'Too Many Requests',
            'parameters' => ['retry_after' => 10],
        ], 429),
    ]);

    $exception = null;

    try {
        Bale::getMe();
    } catch (BaleRequestException $caught) {
        $exception = $caught;
    }

    expect($exception)
        ->toBeInstanceOf(BaleRequestException::class)
        ->and($exception->baleErrorCode)->toBe(429)
        ->and($exception->description)->toBe('Too Many Requests')
        ->and($exception->parameters)->toBe(['retry_after' => 10]);
});

it('fails clearly without a token before making a request', function (): void {
    config(['bale.token' => null]);

    expect(fn (): array => Bale::getMe())
        ->toThrow('BALE_BOT_TOKEN');

    Http::assertNothingSent();
});
