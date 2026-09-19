<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Sajaddp\Bale\Facades\Bale;
use Symfony\Component\HttpFoundation\File\UploadedFile;

beforeEach(function (): void {
    Http::preventStrayRequests();
});
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

it('downloads a Bale file through the documented two-request workflow', function (): void {
    $binaryBody = "\x00Bale\xff\x10";

    Http::fake([
        'https://tapi.bale.ai/bottest-token/getFile' => Http::response([
            'ok' => true,
            'result' => ['file_id' => 'file-1', 'file_path' => 'documents/example.pdf'],
        ]),
        'https://tapi.bale.ai/file/bottest-token/documents/example.pdf' => Http::response($binaryBody),
    ]);

    expect(Bale::downloadFile('file-1'))->toBe($binaryBody);

    Http::assertSentCount(2);
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/bottest-token/getFile'
            && $request->method() === 'POST'
            && $request->data() === ['file_id' => 'file-1'];
    });
    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://tapi.bale.ai/file/bottest-token/documents/example.pdf'
            && $request->method() === 'GET';
    });
});

it('rejects unusable Bale file metadata before downloading', function (array $file): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getFile' => Http::response([
            'ok' => true,
            'result' => $file,
        ]),
    ]);

    expect(fn (): string => Bale::downloadFile('file-1'))
        ->toThrow(UnexpectedValueException::class, 'usable file_path');

    Http::assertSentCount(1);
})->with([
    'missing file path' => [['file_id' => 'file-1']],
    'null file path' => [['file_id' => 'file-1', 'file_path' => null]],
    'empty file path' => [['file_id' => 'file-1', 'file_path' => '']],
    'non-string file path' => [['file_id' => 'file-1', 'file_path' => 123]],
]);

it('preserves Laravel HTTP client failures while downloading a Bale file', function (): void {
    Http::fake([
        'https://tapi.bale.ai/bottest-token/getFile' => Http::response([
            'ok' => true,
            'result' => ['file_id' => 'file-1', 'file_path' => 'documents/missing.pdf'],
        ]),
        'https://tapi.bale.ai/file/bottest-token/documents/missing.pdf' => Http::response('Not found', 404),
    ]);

    expect(fn (): string => Bale::downloadFile('file-1'))
        ->toThrow(RequestException::class);
});

it('fails before sending a request when a local upload is unreadable', function (): void {
    $path = sys_get_temp_dir().'/bale-missing-'.bin2hex(random_bytes(8));

    expect(fn (): array => Bale::sendDocument(10, new SplFileInfo($path)))
        ->toThrow(InvalidArgumentException::class, 'does not exist or is not readable');

    Http::assertNothingSent();
});
