---
title: فایل و رسانه بله در لاراول
description: راهنمای شناسهٔ فایل، نشانی، بارگذاری چندبخشی، بارگذاری با `SplFileInfo` و دانلود فایل بله با لاراول بله.
---

# چطور فایل را به بله بارگذاری کنیم؟

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendVoice` و `sendAnimation`، مقدار رشته‌ای فقط یک `file_id` بله یا نشانی اچ‌تی‌تی‌پی است. مسیر محلی به‌شکل رشته‌ای خودکار بارگذاری نمی‌شود.

~~~php
use Sajaddp\Bale\Facades\Bale;
use SplFileInfo;

// استفادهٔ مجدد از فایل موجود بله
Bale::sendDocument(
    chatId: 123456789,
    document: 'bale-file-id',
    options: ['caption' => 'فایل قبلی'],
);

// ارسال نشانی قابل‌دسترسی اچ‌تی‌تی‌پی
Bale::sendVideo(chatId: 123456789, video: 'https://example.test/video.mp4');

// بارگذاری صریح محلی با دادهٔ چندبخشی
Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/report.pdf')),
);
~~~

طبق مستندات فعلی بله، `sendPhoto` علاوه بر مقصد به `from_chat_id` نیاز دارد:

~~~php
Bale::sendPhoto(
    chatId: '@target_channel',
    fromChatId: '@source_channel',
    photo: new SplFileInfo(storage_path('app/example.jpg')),
);
~~~

## ارسال گروه رسانه

برای آلبوم از آرایهٔ خام مستند بله استفاده کنید. هر فایل پیوست محلی باید نامی روشن و ارجاع متناظر `attach://name` داشته باشد.

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

`getFile($fileId)` متد رسمی برای اطلاعات فایل است. `downloadFile($fileId)` متد کمکی لاراول بله است: ابتدا `getFile` را اجرا می‌کند، وجود `file_path` غیرخالی را می‌خواهد و محتوای دودویی را برمی‌گرداند. هیچ‌کدام نشانی دانلودِ حاوی توکن را عمومی نمی‌کنند.

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

لاراول بله به سامانهٔ فایل وابسته نیست؛ ذخیره‌سازی، نام فایل، بررسی گونهٔ محتوا و سیاست دسترسی با برنامه است. برای آزمون بارگذاری چندبخشی و دانلود بدون شبکه، [راهنمای آزمون](testing.md) را ببینید.
