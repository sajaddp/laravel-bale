<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
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
            'reply_to_message_id' => 12,
        ],
    ))->toBe(['message_id' => 99]);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/sendMessage'
            && $request->method() === 'POST'
            && $request->data() === [
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

it('configures, removes, and inspects a webhook using Bale payloads', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/setWebhook' => Http::response(['ok' => true, 'result' => true]),
        'https://tapi.bale.ai/bottest-token/deleteWebhook' => Http::response(['ok' => true, 'result' => true]),
        'https://tapi.bale.ai/bottest-token/getWebhookInfo' => Http::response([
            'ok' => true,
            'result' => ['url' => 'https://example.test/bale/updates'],
        ]),
    ]);

    expect(Bale::setWebhook('https://example.test/bale/updates'))->toBeTrue()
        ->and(Bale::deleteWebhook())->toBeTrue()
        ->and(Bale::getWebhookInfo())->toBe(['url' => 'https://example.test/bale/updates']);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/setWebhook'
            && $request->method() === 'POST'
            && $request->data() === ['url' => 'https://example.test/bale/updates'];
    });

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/deleteWebhook'
            && $request->method() === 'POST'
            && $request->data() === [];
    });

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getWebhookInfo'
            && $request->method() === 'POST'
            && $request->data() === [];
    });
});

it('allows Bale to disable a webhook through its documented empty URL', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/setWebhook' => Http::response(['ok' => true, 'result' => true]),
    ]);

    expect(Bale::setWebhook(''))->toBeTrue();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/setWebhook'
            && $request->method() === 'POST'
            && $request->data() === ['url' => ''];
    });
});

it('forwards and copies messages with Bale required fields', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/forwardMessage' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 101],
        ]),
        'https://tapi.bale.ai/bottest-token/copyMessage' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 102],
        ]),
    ]);

    expect(Bale::forwardMessage(chatId: '@target', fromChatId: 99, messageId: 21))
        ->toBe(['message_id' => 101])
        ->and(Bale::copyMessage(chatId: '@target', fromChatId: 99, messageId: 21))
        ->toBe(['message_id' => 102]);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/forwardMessage'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@target',
                'from_chat_id' => 99,
                'message_id' => 21,
            ];
    });

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/copyMessage'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@target',
                'from_chat_id' => 99,
                'message_id' => 21,
            ];
    });
});

it('sends Bale chat actions without client-side action validation', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendChatAction' => Http::response(['ok' => true, 'result' => true]),
    ]);

    expect(Bale::sendChatAction(chatId: 123456789, action: 'upload_photo'))->toBeTrue();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/sendChatAction'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => 123456789,
                'action' => 'upload_photo',
            ];
    });
});

it('answers callback queries with documented optional fields and required identifier precedence', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/answerCallbackQuery' => Http::response(['ok' => true, 'result' => true]),
    ]);

    expect(Bale::answerCallbackQuery(
        callbackQueryId: 'callback-42',
        options: [
            'callback_query_id' => 'must-not-win',
            'text' => 'انجام شد',
            'show_alert' => true,
        ],
    ))->toBeTrue();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/answerCallbackQuery'
            && $request->method() === 'POST'
            && $request->data() === [
                'callback_query_id' => 'callback-42',
                'text' => 'انجام شد',
                'show_alert' => true,
            ];
    });
});

it('edits Bale messages with documented required values and optional values', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/editMessageText' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 41, 'text' => 'متن جدید'],
        ]),
        'https://tapi.bale.ai/bottest-token/editMessageCaption' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 41, 'caption' => 'زیرنویس جدید'],
        ]),
        'https://tapi.bale.ai/bottest-token/editMessageReplyMarkup' => Http::response([
            'ok' => true,
            'result' => ['message_id' => 41],
        ]),
    ]);

    $replyMarkup = ['inline_keyboard' => [[['text' => 'تایید', 'callback_data' => 'confirm']]]];

    expect(Bale::editMessageText(
        chatId: '@channel',
        messageId: 41,
        text: 'متن جدید',
        options: [
            'chat_id' => '@other_channel',
            'message_id' => 999,
            'text' => 'نباید ارسال شود',
            'reply_markup' => $replyMarkup,
        ],
    ))->toBe(['message_id' => 41, 'text' => 'متن جدید'])
        ->and(Bale::editMessageCaption(
            chatId: '@channel',
            messageId: 41,
            options: [
                'chat_id' => '@other_channel',
                'message_id' => 999,
                'caption' => 'زیرنویس جدید',
                'reply_markup' => $replyMarkup,
            ],
        ))->toBe(['message_id' => 41, 'caption' => 'زیرنویس جدید'])
        ->and(Bale::editMessageReplyMarkup(
            chatId: '@channel',
            messageId: 41,
            options: [
                'chat_id' => '@other_channel',
                'message_id' => 999,
                'reply_markup' => $replyMarkup,
            ],
        ))->toBe(['message_id' => 41]);

    Http::assertSent(function (Request $request) use ($replyMarkup): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/editMessageText'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@channel',
                'message_id' => 41,
                'text' => 'متن جدید',
                'reply_markup' => $replyMarkup,
            ];
    });

    Http::assertSent(function (Request $request) use ($replyMarkup): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/editMessageCaption'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@channel',
                'message_id' => 41,
                'caption' => 'زیرنویس جدید',
                'reply_markup' => $replyMarkup,
            ];
    });

    Http::assertSent(function (Request $request) use ($replyMarkup): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/editMessageReplyMarkup'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@channel',
                'message_id' => 41,
                'reply_markup' => $replyMarkup,
            ];
    });
});

it('deletes messages without duplicating Bale server-side restrictions', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/deleteMessage' => Http::response(['ok' => true, 'result' => true]),
    ]);

    expect(Bale::deleteMessage(chatId: '@channel', messageId: 41))->toBeTrue();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/deleteMessage'
            && $request->method() === 'POST'
            && $request->data() === [
                'chat_id' => '@channel',
                'message_id' => 41,
            ];
    });
});

it('does not return a successful Bale result from an unsuccessful HTTP response', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => true,
            'result' => ['id' => 42],
        ], 500),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(RequestException::class);
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

it('rejects malformed successful result shapes', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/deleteWebhook' => Http::response(['ok' => true, 'result' => 'true']),
    ]);

    expect(fn (): bool => Bale::deleteWebhook())
        ->toThrow(UnexpectedValueException::class, 'array or boolean result');
});

it('fails clearly without a token before making a request', function (): void {
    config(['bale.token' => null]);

    expect(fn (): array => Bale::getMe())
        ->toThrow('BALE_BOT_TOKEN');

    Http::assertNothingSent();
});
