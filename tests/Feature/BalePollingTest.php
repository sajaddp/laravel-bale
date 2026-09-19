<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Sajaddp\Bale\Facades\Bale;

beforeEach(function (): void {
    Http::preventStrayRequests();
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

it('rejects malformed getUpdates result lists', function (string|array $result): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getUpdates' => Http::response([
            'ok' => true,
            'result' => $result,
        ]),
    ]);

    expect(fn (): array => Bale::getUpdates())
        ->toThrow(UnexpectedValueException::class);
})->with([
    'response object instead of a list' => [['update_id' => 42]],
    'list with a scalar item' => [['not an update']],
]);

it('gives Bale long-polling timeouts transport headroom without changing the payload', function (int $baleTimeout, int $expectedTransportTimeout): void {
    $transportTimeout = null;

    Http::fake(function (Request $request, array $options) use (&$transportTimeout) {
        $transportTimeout = $options['timeout'] ?? null;

        return Http::response(['ok' => true, 'result' => []]);
    });

    expect(Bale::getUpdates(['timeout' => $baleTimeout]))->toBe([])
        ->and($transportTimeout)->toBe($expectedTransportTimeout);

    Http::assertSent(function (Request $request) use ($baleTimeout): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getUpdates'
            && $request->isJson()
            && $request->data() === ['timeout' => $baleTimeout];
    });
})->with([
    'sixty-second long poll' => [60, 65],
    'thirty-second boundary' => [30, 35],
]);

it('disables the transport timeout beyond the Guzzle millisecond boundary', function (): void {
    $largestSafeGuzzleTimeout = intdiv(PHP_INT_MAX, 1000) - 1;
    $largestSafeBaleTimeout = $largestSafeGuzzleTimeout - 5;
    $firstUnsafeBaleTimeout = $largestSafeBaleTimeout + 1;
    $transportTimeouts = [];

    Http::fake(function (Request $request, array $options) use (&$transportTimeouts) {
        $transportTimeouts[] = $options['timeout'] ?? null;

        return Http::response(['ok' => true, 'result' => []]);
    });

    expect(Bale::getUpdates(['timeout' => $largestSafeBaleTimeout]))->toBe([])
        ->and(Bale::getUpdates(['timeout' => $firstUnsafeBaleTimeout]))->toBe([])
        ->and($transportTimeouts)->toBe([$largestSafeGuzzleTimeout, 0]);

    Http::assertSentInOrder([
        function (Request $request) use ($largestSafeBaleTimeout): bool {
            return $request->data() === ['timeout' => $largestSafeBaleTimeout];
        },
        function (Request $request) use ($firstUnsafeBaleTimeout): bool {
            return $request->data() === ['timeout' => $firstUnsafeBaleTimeout];
        },
    ]);
});

it('preserves an extreme Bale timeout while disabling the transport timeout', function (): void {
    $transportTimeout = null;

    Http::fake(function (Request $request, array $options) use (&$transportTimeout) {
        $transportTimeout = $options['timeout'] ?? null;

        return Http::response(['ok' => true, 'result' => []]);
    });

    expect(Bale::getUpdates(['timeout' => PHP_INT_MAX]))->toBe([])
        ->and($transportTimeout)->toBe(0);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getUpdates'
            && $request->isJson()
            && $request->data() === ['timeout' => PHP_INT_MAX];
    });
});
