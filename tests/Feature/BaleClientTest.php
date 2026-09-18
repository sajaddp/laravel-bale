<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Sajaddp\Bale\BaleClient;
use Sajaddp\Bale\BaleServiceProvider;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use Sajaddp\Bale\Facades\Bale;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
            'result' => true,
        ]),
        'https://tapi.bale.ai/bottest-token/editMessageCaption' => Http::response([
            'ok' => true,
            'result' => 'caption-updated',
        ]),
        'https://tapi.bale.ai/bottest-token/editMessageReplyMarkup' => Http::response([
            'ok' => true,
            'result' => 41,
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
    ))->toBeTrue()
        ->and(Bale::editMessageCaption(
            chatId: '@channel',
            messageId: 41,
            options: [
                'chat_id' => '@other_channel',
                'message_id' => 999,
                'caption' => 'زیرنویس جدید',
                'reply_markup' => $replyMarkup,
            ],
        ))->toBe('caption-updated')
        ->and(Bale::editMessageReplyMarkup(
            chatId: '@channel',
            messageId: 41,
            options: [
                'chat_id' => '@other_channel',
                'message_id' => 999,
                'reply_markup' => $replyMarkup,
            ],
        ))->toBe(41);

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

it('throws an inspectable exception for Bale API failures with an integer retry_after', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response('{"ok":false,"error_code":429,"description":"Too Many Requests","parameters":{"retry_after":10}}', 429),
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
        ->and($exception->parameters['retry_after'])->toBe(10);
});

it('accepts an empty ResponseParameters object', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response('{"ok":false,"error_code":400,"parameters":{}}'),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(BaleRequestException::class);
});

it('accepts unknown ResponseParameters object properties', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response('{"ok":false,"error_code":400,"parameters":{"future_field":"value"}}'),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(BaleRequestException::class);
});

it('rejects malformed successful result shapes', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/deleteWebhook' => Http::response(['ok' => true, 'result' => 'true']),
    ]);

    expect(fn (): bool => Bale::deleteWebhook())
        ->toThrow(UnexpectedValueException::class, 'boolean result');
});

it('rejects successful Bale responses without a result', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response(['ok' => true]),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(UnexpectedValueException::class, 'without a result');
});

it('rejects non-boolean Bale ok values', function (bool|int|string $ok): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => $ok,
            'result' => [],
        ]),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(UnexpectedValueException::class, 'invalid API response');
})->with([
    'integer ok' => 1,
    'string ok' => 'true',
]);

it('rejects malformed Bale error envelopes', function (array $payload): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response($payload),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(UnexpectedValueException::class, 'invalid API error response');
})->with([
    'missing error code' => [['ok' => false]],
    'string error code' => [['ok' => false, 'error_code' => '429']],
    'non-string description' => [['ok' => false, 'error_code' => 429, 'description' => []]],
    'non-array parameters' => [['ok' => false, 'error_code' => 429, 'parameters' => 'retry']],
]);

it('rejects ResponseParameters lists', function (string $parameters): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response('{"ok":false,"error_code":400,"parameters":'.$parameters.'}'),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(UnexpectedValueException::class, 'invalid API error response');
})->with([
    'empty list' => ['[]'],
    'indexed list' => ['[10]'],
    'object list' => ['[{"retry_after":10}]'],
]);

it('rejects malformed Bale retry_after values', function (mixed $retryAfter): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => false,
            'error_code' => 429,
            'parameters' => ['retry_after' => $retryAfter],
        ]),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(UnexpectedValueException::class, 'invalid API error response');
})->with([
    'string retry after' => ['10'],
    'array retry after' => [[]],
    'null retry after' => [null],
]);

it('keeps an omitted Bale error description valid', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => false,
            'error_code' => 400,
        ]),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(BaleRequestException::class, 'Bale API request failed.');
});

it('preserves Laravel HTTP failures for malformed Bale retry_after values', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response([
            'ok' => false,
            'error_code' => 429,
            'parameters' => ['retry_after' => '10'],
        ], 429),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(RequestException::class);
});

it('preserves Laravel HTTP failures for malformed ResponseParameters lists', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getMe' => Http::response('{"ok":false,"error_code":400,"parameters":[]}', 400),
    ]);

    expect(fn (): array => Bale::getMe())
        ->toThrow(RequestException::class);
});

it('fails clearly without a token before making a request', function (): void {
    config(['bale.token' => null]);

    expect(fn (): array => Bale::getMe())
        ->toThrow('BALE_BOT_TOKEN');

    Http::assertNothingSent();
});

it('gets updates with one JSON request and forwards documented options', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getUpdates' => Http::response([
            'ok' => true,
            'result' => [['update_id' => 42]],
        ]),
    ]);

    expect(Bale::getUpdates())->toBe([['update_id' => 42]]);

    Bale::getUpdates(['offset' => 43, 'limit' => 10, 'timeout' => 30]);

    Http::assertSentCount(2);
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getUpdates'
            && $request->method() === 'POST'
            && $request->isJson()
            && $request->data() === [];
    });
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getUpdates'
            && $request->isJson()
            && $request->data() === ['offset' => 43, 'limit' => 10, 'timeout' => 30];
    });
});

it('gives Bale long-polling timeouts transport headroom without changing the payload', function (int $baleTimeout): void {
    $transportTimeout = null;

    Http::fake(function (Request $request, array $options) use (&$transportTimeout) {
        $transportTimeout = $options['timeout'] ?? null;

        return Http::response(['ok' => true, 'result' => []]);
    });

    expect(Bale::getUpdates(['timeout' => $baleTimeout]))->toBe([])
        ->and($transportTimeout)->toBeInt()->toBeGreaterThan($baleTimeout);

    Http::assertSent(function (Request $request) use ($baleTimeout): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getUpdates'
            && $request->isJson()
            && $request->data() === ['timeout' => $baleTimeout];
    });
})->with([
    'sixty-second long poll' => [60],
    'thirty-second boundary' => [30],
]);

it('sends every single media type as JSON with its documented fields', function (Closure $call, string $endpoint, array $expected): void {
    Http::fake([
        "https://tapi.bale.ai/bottest-token/{$endpoint}" => Http::response([
            'ok' => true,
            'result' => ['message_id' => 300],
        ]),
    ]);

    expect($call())->toBe(['message_id' => 300]);

    Http::assertSent(function (Request $request) use ($endpoint, $expected): bool {
        return $request->url() === "https://tapi.bale.ai/bottest-token/{$endpoint}"
            && $request->method() === 'POST'
            && $request->isJson()
            && $request->data() === $expected;
    });
})->with([
    'photo' => [
        fn (): array => Bale::sendPhoto(
            chatId: '@target',
            fromChatId: '@source',
            photo: 'photo-file-id',
            options: ['caption' => 'تصویر'],
        ),
        'sendPhoto',
        ['caption' => 'تصویر', 'from_chat_id' => '@source', 'chat_id' => '@target', 'photo' => 'photo-file-id'],
    ],
    'audio' => [
        fn (): array => Bale::sendAudio(chatId: 10, audio: 'https://example.test/song.m4a', options: ['caption' => 'صوت']),
        'sendAudio',
        ['caption' => 'صوت', 'chat_id' => 10, 'audio' => 'https://example.test/song.m4a'],
    ],
    'document' => [
        fn (): array => Bale::sendDocument(chatId: 10, document: 'document-file-id', options: ['reply_to_message_id' => 11]),
        'sendDocument',
        ['reply_to_message_id' => 11, 'chat_id' => 10, 'document' => 'document-file-id'],
    ],
    'video' => [
        fn (): array => Bale::sendVideo(chatId: 10, video: 'https://example.test/video.mp4', options: ['caption' => 'ویدیو']),
        'sendVideo',
        ['caption' => 'ویدیو', 'chat_id' => 10, 'video' => 'https://example.test/video.mp4'],
    ],
    'animation' => [
        fn (): array => Bale::sendAnimation(chatId: 10, animation: 'animation-file-id', options: ['reply_to_message_id' => 12]),
        'sendAnimation',
        ['reply_to_message_id' => 12, 'chat_id' => 10, 'animation' => 'animation-file-id'],
    ],
    'voice' => [
        fn (): array => Bale::sendVoice(chatId: 10, voice: 'https://example.test/voice.ogg', options: ['caption' => 'پیام صوتی']),
        'sendVoice',
        ['caption' => 'پیام صوتی', 'chat_id' => 10, 'voice' => 'https://example.test/voice.ogg'],
    ],
]);

it('gives required media arguments precedence over options', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendPhoto' => Http::response(['ok' => true, 'result' => ['message_id' => 301]]),
    ]);

    Bale::sendPhoto(
        chatId: '@target',
        fromChatId: '@source',
        photo: 'file-id',
        options: ['chat_id' => '@other', 'from_chat_id' => '@other_source', 'photo' => 'other-file'],
    );

    Http::assertSent(function (Request $request): bool {
        return $request->data() === [
            'chat_id' => '@target',
            'from_chat_id' => '@source',
            'photo' => 'file-id',
        ];
    });
});

it('does not auto-detect a local path string as a file upload', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-path-string-');
    file_put_contents($path, 'Bale local path string');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendDocument' => Http::response(['ok' => true, 'result' => ['message_id' => 301]]),
    ]);

    try {
        Bale::sendDocument(10, $path);

        Http::assertSent(function (Request $request) use ($path): bool {
            return $request->isJson()
                && $request->data() === ['chat_id' => 10, 'document' => $path];
        });
    } finally {
        unlink($path);
    }
});

it('uploads every single media type as multipart with the documented file field', function (Closure $call, string $endpoint, string $field): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-media-');
    file_put_contents($path, 'Bale multipart media');
    $file = new SplFileInfo($path);

    Http::fake([
        "https://tapi.bale.ai/bottest-token/{$endpoint}" => Http::response(['ok' => true, 'result' => ['message_id' => 302]]),
    ]);

    try {
        expect($call($file))->toBe(['message_id' => 302]);

        Http::assertSent(function (Request $request) use ($endpoint, $field, $path): bool {
            return $request->url() === "https://tapi.bale.ai/bottest-token/{$endpoint}"
                && $request->method() === 'POST'
                && $request->isMultipart()
                && $request->hasFile($field, null, basename($path))
                && str_contains($request->body(), 'Bale multipart media');
        });
    } finally {
        unlink($path);
    }
})->with([
    'photo' => [fn (SplFileInfo $file): array => Bale::sendPhoto(10, 11, $file), 'sendPhoto', 'photo'],
    'audio' => [fn (SplFileInfo $file): array => Bale::sendAudio(10, $file), 'sendAudio', 'audio'],
    'document' => [fn (SplFileInfo $file): array => Bale::sendDocument(10, $file), 'sendDocument', 'document'],
    'video' => [fn (SplFileInfo $file): array => Bale::sendVideo(10, $file), 'sendVideo', 'video'],
    'animation' => [fn (SplFileInfo $file): array => Bale::sendAnimation(10, $file), 'sendAnimation', 'animation'],
    'voice' => [fn (SplFileInfo $file): array => Bale::sendVoice(10, $file), 'sendVoice', 'voice'],
]);

it('accepts Symfony uploaded files and keeps their client filename', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-uploaded-');
    file_put_contents($path, 'Bale uploaded file');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendDocument' => Http::response(['ok' => true, 'result' => ['message_id' => 302]]),
    ]);

    try {
        Bale::sendDocument(10, new UploadedFile($path, 'original-document.pdf', test: true));

        Http::assertSent(function (Request $request): bool {
            return $request->isMultipart()
                && $request->hasFile('document', null, 'original-document.pdf');
        });
    } finally {
        unlink($path);
    }
});

it('JSON serializes structured multipart fields without bracket encoding', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-document-');
    file_put_contents($path, 'Bale document');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendDocument' => Http::response(['ok' => true, 'result' => ['message_id' => 303]]),
    ]);

    try {
        Bale::sendDocument(10, new SplFileInfo($path), [
            'caption' => 'ضمیمه',
            'reply_markup' => ['inline_keyboard' => [[['text' => 'تایید', 'callback_data' => 'ok']]]],
        ]);

        Http::assertSent(function (Request $request): bool {
            return $request->isMultipart()
                && str_contains($request->body(), 'name="caption"')
                && str_contains($request->body(), 'ضمیمه')
                && str_contains($request->body(), 'name="reply_markup"')
                && str_contains($request->body(), '{"inline_keyboard"')
                && ! str_contains($request->body(), 'reply_markup[inline_keyboard]');
        });
    } finally {
        unlink($path);
    }
});

it('sends a media group as JSON when it has no local attachments', function (): void {
    $media = [
        ['type' => 'photo', 'media' => 'photo-file-id', 'caption' => 'اولی'],
        ['type' => 'video', 'media' => 'https://example.test/video.mp4'],
    ];

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMediaGroup' => Http::response([
            'ok' => true,
            'result' => [['message_id' => 304], ['message_id' => 305]],
        ]),
    ]);

    expect(Bale::sendMediaGroup(chatId: '@target', media: $media, options: ['reply_to_message_id' => 42]))
        ->toBe([['message_id' => 304], ['message_id' => 305]]);

    Http::assertSent(function (Request $request) use ($media): bool {
        return $request->isJson()
            && $request->data() === [
                'reply_to_message_id' => 42,
                'chat_id' => '@target',
                'media' => $media,
            ];
    });
});

it('rejects a missing main media attachment before sending a request', function (): void {
    expect(fn (): array => Bale::sendMediaGroup(
        chatId: 10,
        media: [['type' => 'photo', 'media' => 'attach://missing']],
    ))->toThrow(InvalidArgumentException::class, 'does not have a matching local file');

    Http::assertNothingSent();
});

it('rejects a missing documented thumbnail attachment before sending a request', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-other-');
    file_put_contents($path, 'other attachment');

    try {
        expect(fn (): array => Bale::sendMediaGroup(
            chatId: 10,
            media: [[
                'type' => 'video',
                'media' => 'video-file-id',
                'thumbnail' => 'attach://thumb',
            ]],
            attachments: ['other' => new SplFileInfo($path)],
        ))->toThrow(InvalidArgumentException::class, 'does not have a matching local file');

        Http::assertNothingSent();
    } finally {
        unlink($path);
    }
});

it('sends matching media and thumbnail attachments as multipart', function (): void {
    $videoPath = tempnam(sys_get_temp_dir(), 'bale-video-');
    $thumbnailPath = tempnam(sys_get_temp_dir(), 'bale-thumbnail-');
    file_put_contents($videoPath, 'video attachment');
    file_put_contents($thumbnailPath, 'thumbnail attachment');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMediaGroup' => Http::response([
            'ok' => true,
            'result' => [['message_id' => 306]],
        ]),
    ]);

    try {
        expect(Bale::sendMediaGroup(
            chatId: 10,
            media: [[
                'type' => 'video',
                'media' => 'attach://video',
                'thumbnail' => 'attach://thumb',
            ]],
            attachments: [
                'video' => new SplFileInfo($videoPath),
                'thumb' => new SplFileInfo($thumbnailPath),
            ],
        ))->toBe([['message_id' => 306]]);

        Http::assertSent(function (Request $request) use ($videoPath, $thumbnailPath): bool {
            return $request->isMultipart()
                && $request->hasFile('video', null, basename($videoPath))
                && $request->hasFile('thumb', null, basename($thumbnailPath))
                && str_contains($request->body(), '"media":"attach:\/\/video"')
                && str_contains($request->body(), '"thumbnail":"attach:\/\/thumb"');
        });
    } finally {
        unlink($videoPath);
        unlink($thumbnailPath);
    }
});

it('supports dotted multipart attachment names', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-dotted-');
    file_put_contents($path, 'dotted attachment');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMediaGroup' => Http::response(['ok' => true, 'result' => [['message_id' => 307]]]),
    ]);

    try {
        Bale::sendMediaGroup(
            chatId: 10,
            media: [['type' => 'photo', 'media' => 'attach://cover.thumb']],
            attachments: ['cover.thumb' => new SplFileInfo($path)],
        );

        Http::assertSent(function (Request $request) use ($path): bool {
            return $request->isMultipart()
                && $request->hasFile('cover.thumb', null, basename($path));
        });
    } finally {
        unlink($path);
    }
});

it('normalizes numeric attachment keys to multipart field names', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-numeric-');
    file_put_contents($path, 'numeric attachment');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMediaGroup' => Http::response(['ok' => true, 'result' => [['message_id' => 308]]]),
    ]);

    try {
        Bale::sendMediaGroup(
            chatId: 10,
            media: [['type' => 'photo', 'media' => 'attach://1']],
            attachments: [1 => new SplFileInfo($path)],
        );

        Http::assertSent(function (Request $request) use ($path): bool {
            return $request->isMultipart()
                && $request->hasFile('1', null, basename($path));
        });
    } finally {
        unlink($path);
    }
});

it('sends media group attachments as multipart and JSON serializes media', function (): void {
    $firstPath = tempnam(sys_get_temp_dir(), 'bale-first-');
    $secondPath = tempnam(sys_get_temp_dir(), 'bale-second-');
    file_put_contents($firstPath, 'first image');
    file_put_contents($secondPath, 'second image');
    $media = [
        ['type' => 'photo', 'media' => 'attach://first'],
        ['type' => 'photo', 'media' => 'attach://second'],
    ];

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendMediaGroup' => Http::response([
            'ok' => true,
            'result' => [['message_id' => 306], ['message_id' => 307]],
        ]),
    ]);

    try {
        expect(Bale::sendMediaGroup(
            chatId: 10,
            media: $media,
            attachments: ['first' => new SplFileInfo($firstPath), 'second' => new SplFileInfo($secondPath)],
        ))->toBe([['message_id' => 306], ['message_id' => 307]]);

        Http::assertSent(function (Request $request) use ($firstPath, $secondPath): bool {
            return $request->isMultipart()
                && $request->hasFile('first', null, basename($firstPath))
                && $request->hasFile('second', null, basename($secondPath))
                && str_contains($request->body(), 'first image')
                && str_contains($request->body(), 'second image')
                && str_contains($request->body(), 'name="media"')
                && str_contains($request->body(), '{"type":"photo","media":"attach:\/\/first"}')
                && ! str_contains($request->body(), 'media[0]');
        });
    } finally {
        unlink($firstPath);
        unlink($secondPath);
    }
});

it('sends documented location and contact fields and gets file metadata', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendLocation' => Http::response(['ok' => true, 'result' => ['message_id' => 308]]),
        'https://tapi.bale.ai/bottest-token/sendContact' => Http::response(['ok' => true, 'result' => ['message_id' => 309]]),
        'https://tapi.bale.ai/bottest-token/getFile' => Http::response(['ok' => true, 'result' => ['file_id' => 'file-1', 'file_path' => 'files/1']]),
    ]);

    expect(Bale::sendLocation(10, 35.7, 51.4, [
        'horizontal_accuracy' => 12.5,
        'chat_id' => 99,
        'latitude' => 1.0,
        'longitude' => 2.0,
    ]))->toBe(['message_id' => 308])
        ->and(Bale::sendContact(10, '+989120000000', 'سجاد', [
            'last_name' => 'دهشیری',
            'chat_id' => 99,
            'phone_number' => 'other',
            'first_name' => 'دیگر',
        ]))->toBe(['message_id' => 309])
        ->and(Bale::getFile('file-1'))->toBe(['file_id' => 'file-1', 'file_path' => 'files/1']);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/sendLocation'
            && $request->data() === [
                'horizontal_accuracy' => 12.5,
                'chat_id' => 10,
                'latitude' => 35.7,
                'longitude' => 51.4,
            ];
    });
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/sendContact'
            && $request->data() === [
                'last_name' => 'دهشیری',
                'chat_id' => 10,
                'phone_number' => '+989120000000',
                'first_name' => 'سجاد',
            ];
    });
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getFile'
            && $request->data() === ['file_id' => 'file-1'];
    });
});

it('fails before sending a request when a local upload is unreadable', function (): void {
    $path = sys_get_temp_dir().'/bale-missing-'.bin2hex(random_bytes(8));

    expect(fn (): array => Bale::sendDocument(10, new SplFileInfo($path)))
        ->toThrow(InvalidArgumentException::class, 'does not exist or is not readable');

    Http::assertNothingSent();
});

it('uses the shared Bale error parser for multipart requests', function (mixed $response, int $status, string $exception): void {
    $path = tempnam(sys_get_temp_dir(), 'bale-error-');
    file_put_contents($path, 'Bale error test');

    Http::fake([
        'https://tapi.bale.ai/bottest-token/sendDocument' => Http::response($response, $status),
    ]);

    try {
        expect(fn (): array => Bale::sendDocument(10, new SplFileInfo($path)))->toThrow($exception);
    } finally {
        unlink($path);
    }
})->with([
    'valid Bale error' => [['ok' => false, 'error_code' => 400, 'description' => 'Bad Request'], 400, BaleRequestException::class],
    'malformed success envelope' => [['ok' => 'true', 'result' => []], 200, UnexpectedValueException::class],
    'malformed ResponseParameters' => [['ok' => false, 'error_code' => 429, 'parameters' => ['retry_after' => '10']], 200, UnexpectedValueException::class],
    'HTTP failure outside the Bale envelope' => [['ok' => true, 'result' => ['message_id' => 1]], 500, RequestException::class],
    'wrong typed result' => [['ok' => true, 'result' => true], 200, UnexpectedValueException::class],
]);
