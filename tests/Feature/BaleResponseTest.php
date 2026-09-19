<?php

declare(strict_types=1);

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Sajaddp\Bale\Exceptions\BaleRequestException;
use Sajaddp\Bale\Facades\Bale;

beforeEach(function (): void {
    Http::preventStrayRequests();
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
