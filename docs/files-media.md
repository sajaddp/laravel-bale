---
title: فایل و رسانه بله در Laravel
description: راهنمای file_id، URL، multipart، آپلود با SplFileInfo و دانلود فایل بله با Laravel Bale.
---

# چطور فایل را به بله آپلود کنیم؟

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendVoice` و `sendAnimation`، مقدار رشته‌ای فقط یک `file_id` بله یا HTTP URL است. مسیر محلی به‌شکل رشته‌ای خودکار آپلود نمی‌شود.

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

## ارسال گروه رسانه

برای آلبوم از آرایهٔ خام مستند Bale استفاده کنید. هر فایل پیوست محلی باید نامی روشن و ارجاع متناظر `attach://name` داشته باشد.

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

`getFile($fileId)` متد رسمی برای اطلاعات فایل است. `downloadFile($fileId)` متد کمکی Laravel Bale است: ابتدا `getFile` را اجرا می‌کند، وجود `file_path` غیرخالی را می‌خواهد و محتوای دودویی را برمی‌گرداند. هیچ‌کدام URL دانلودِ حاوی توکن را عمومی نمی‌کنند.

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

Laravel Bale به Filesystem وابسته نیست؛ ذخیره‌سازی، نام فایل، بررسی MIME و سیاست دسترسی با برنامه است. برای تست `multipart` و دانلود بدون شبکه، [راهنمای تست](testing.md) را ببینید.
