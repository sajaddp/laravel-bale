---
title: فایل و رسانه بله در Laravel
description: راهنمای file_id، URL، multipart، آپلود SplFileInfo و دانلود فایل بله با Laravel Bale.
---

# چطور فایل را به بله آپلود کنیم؟

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendVoice` و `sendAnimation`، مقدار `string` فقط یک `file_id` بله یا HTTP URL است. مسیر محلی به شکل string خودکار upload نمی‌شود.

~~~php
use Sajaddp\Bale\Facades\Bale;
use SplFileInfo;

// استفادهٔ مجدد از فایل موجود Bale
Bale::sendDocument(
    chatId: 123456789,
    document: 'bale-file-id',
    options: ['caption' => 'فایل قبلی'],
);

// ارسال URL قابل‌دسترسی HTTP
Bale::sendVideo(chatId: 123456789, video: 'https://example.test/video.mp4');

// آپلود صریح محلی با multipart/form-data
Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/report.pdf')),
);
~~~

طبق مستندات فعلی Bale، `sendPhoto` علاوه بر مقصد به `from_chat_id` نیاز دارد:

~~~php
Bale::sendPhoto(
    chatId: '@target_channel',
    fromChatId: '@source_channel',
    photo: new SplFileInfo(storage_path('app/example.jpg')),
);
~~~

## ارسال media group

برای album از شکل raw array مستند Bale استفاده کنید. هر attachment محلی نام صریح و reference متناظر `attach://name` دارد.

~~~php
Bale::sendMediaGroup(
    chatId: 123456789,
    media: [
        ['type' => 'photo', 'media' => 'attach://first'],
        ['type' => 'photo', 'media' => 'attach://second'],
    ],
    attachments: [
        'first' => new SplFileInfo(storage_path('app/first.jpg')),
        'second' => new SplFileInfo(storage_path('app/second.jpg')),
    ],
);
~~~

## چطور فایل دریافتی بله را دانلود کنیم؟

`getFile($fileId)` wrapper رسمی برای metadata فایل است. `downloadFile($fileId)` convenience Laravel Bale است: نخست `getFile` را اجرا می‌کند، `file_path` غیرخالی را می‌خواهد و binary body را برمی‌گرداند. هیچ‌یک URL دانلود حاوی توکن را public نمی‌کنند.

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

Laravel Bale به Filesystem وابسته نیست؛ ذخیره‌سازی، نام فایل، MIME validation و access policy با application است. برای تست multipart و دانلود بدون شبکه، [راهنمای تست](testing.md) را ببینید.
