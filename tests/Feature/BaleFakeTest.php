<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\AssertionFailedError;
use Sajaddp\Bale\Facades\Bale;

beforeEach(function (): void {
    Http::preventStrayRequests();
});
it('fakes Bale requests while preserving real request construction and result contracts', function (): void {
    Bale::fake();

    expect(Bale::sendMessage(chatId: 123456789, text: 'سلام', options: ['reply_to_message_id' => 7]))->toBeArray()
        ->and(Bale::sendChatAction(chatId: 123456789, action: 'typing'))->toBeTrue()
        ->and(Bale::getMe())->toBeArray()
        ->and(Bale::editMessageText(chatId: 123456789, messageId: 42, text: 'ویرایش'))->toBeTrue()
        ->and(Bale::askReview(userId: 123456789, delaySeconds: 5))->toBeTrue();

    Bale::assertSent('sendMessage', [
        'chat_id' => 123456789,
        'text' => 'سلام',
    ]);
    Bale::assertSentTimes('sendMessage', 1);
    Bale::assertNotSent('sendDocument');
    Bale::assertNotSent('sendMessage', ['text' => 'ارسال نشده']);
});

it('supports advanced Bale assertions with Laravel HTTP client requests', function (): void {
    Bale::fake();

    Bale::sendDocument(
        chatId: 123456789,
        document: new SplFileInfo(dirname(__DIR__).'/Fixtures/upload.txt'),
        options: ['caption' => 'فایل'],
    );

    Bale::assertSent('sendDocument', fn (Request $request): bool => $request->isMultipart()
        && str_contains($request->body(), 'filename="upload.txt"'));
});

it('records the official request made by a convenience method under the Bale fake', function (): void {
    Bale::fake();

    Bale::replyToMessage(
        message: ['message_id' => 12, 'chat' => ['id' => 123456789]],
        text: 'پاسخ',
    );

    Bale::assertSent('sendMessage', [
        'chat_id' => 123456789,
        'reply_to_message_id' => 12,
    ]);
    Bale::assertNotSent('replyToMessage');
});

it('fakes getFile and its binary download flow', function (): void {
    Bale::fake();

    expect(Bale::getFile('file-id'))->toMatchArray([
        'file_id' => 'bale-fake-file',
        'file_path' => 'bale-fake/file',
    ])->and(Bale::downloadFile('file-id'))->toBe('Bale fake file download');

    Bale::assertSentTimes('getFile', 2);
});

it('does not count unrelated Laravel HTTP traffic as Bale traffic', function (): void {
    Bale::fake();
    Http::fake([
        'https://example.test/*' => Http::response(['ok' => true]),
    ]);

    expect(Http::get('https://example.test/health')->json())->toBe(['ok' => true]);

    Bale::assertNothingSent();
    Bale::assertNotSent('sendMessage');
});

it('lets the Bale fake handle Bale traffic before a later catch-all Laravel fake', function (): void {
    Bale::fake();
    Http::fake([
        '*' => Http::response('external'),
    ]);

    expect(Http::get('https://example.test/health')->body())->toBe('external');

    Bale::assertNothingSent();

    expect(Bale::getMe())->toBeArray();

    Bale::assertSent('getMe');
    Bale::assertSentTimes('getMe', 1);
    Bale::assertNotSent('sendMessage');
});

it('resets Bale fake request history when activated again', function (): void {
    Bale::fake();
    Bale::getMe();
    Bale::assertSent('getMe');

    Bale::fake();

    Bale::assertNothingSent();
});

it('reports the expected Bale method in failed fake assertions without exposing request URLs', function (): void {
    Bale::fake();

    expect(function (): void {
        Bale::assertSent('sendMessage');
    })
        ->toThrow(AssertionFailedError::class, 'Expected Bale method [sendMessage] to be sent.');

    expect(function (): void {
        Bale::assertSentTimes('sendMessage', 2);
    })
        ->toThrow(AssertionFailedError::class, 'Expected Bale method [sendMessage] to be sent 2 times.');
});

it('does not accept invented Bale APIs while faking', function (): void {
    Bale::fake();

    expect(fn (): mixed => Bale::thisMethodDoesNotExist())
        ->toThrow(Error::class);
});

it('documents every public Bale testing API in the README', function (): void {
    $readme = file_get_contents(dirname(__DIR__, 2).'/README.md');

    expect($readme)->toContain(
        'Bale::fake()',
        'Bale::assertSent',
        'Bale::assertSentTimes',
        'Bale::assertNotSent',
        'Bale::assertNothingSent',
    );
});
