---
title: رفع اشکال Laravel Bale
description: خطاهای پرتکرار Laravel Bale برای توکن، Webhook، فایل، polling و مرز Bale با Telegram.
---

# رفع اشکال

## `BALE_BOT_TOKEN` تنظیم نشده است

پکیج پیش از ساخت URL، توکن خالی یا غیررشته‌ای را با `LogicException` رد می‌کند. `BALE_BOT_TOKEN` را در محیط برنامه تنظیم کنید و config cache برنامه را متناسب با deployment خود refresh کنید.

## نوع exception چیست؟

- `BaleRequestException`: Bale یک envelope معتبر با `ok: false` برگردانده است.
- `RequestException`: پاسخ HTTP ناموفق یا دانلود binary ناموفق است که envelope معتبر Bale ندارد.
- `UnexpectedValueException`: پاسخ موفق، `result` یا metadata فایل با contract مورد انتظار سازگار نیست.

## Webhook 419 یا URL اشتباه

در `routes/web.php`، POST خارجی Bale CSRF token ندارد. دقیقاً همان URI webhook را در `bootstrap/app.php` از CSRF خارج کنید؛ مثلاً `bale/webhook`. یا route را با طراحی امنیتی خودتان در محل مناسب قرار دهید. URL داده‌شده به `setWebhook` باید URL عمومی دقیق همان route باشد. پکیج route، controller یا secret validation نمی‌سازد.

## مسیر محلی رشته‌ای upload نمی‌شود

`string` در API رسانه فقط Bale `file_id` یا HTTP URL است. برای upload، `SplFileInfo` بدهید:

~~~php
new SplFileInfo(storage_path('app/file.pdf'))
~~~

## `replyToMessage` خطا می‌دهد

ورودی باید raw Bale Message با `message_id` integer و `chat.id` integer باشد. object، string یا Message ناقص را تبدیل نکنید؛ دادهٔ Update را همان‌طور که دریافت شده بررسی کنید.

## فایل دانلود نشد

`downloadFile` ابتدا `getFile` می‌زند. نتیجه باید `file_path` غیرخالی داشته باشد، وگرنه `UnexpectedValueException` رخ می‌دهد. URL دانلود حاوی توکن است؛ آن را log یا public نکنید. برای حفظ لینک تازه، در هر دریافت از `getFile` جدید استفاده کنید.

## `attach://` برای media group کار نمی‌کند

هر reference مانند `attach://first` باید یک attachment با کلید دقیق `first` و `SplFileInfo` قابل‌خواندن داشته باشد. برای upload multipart، فیلدهای JSON مانند `media` را پکیج serialize می‌کند.

## Polling بیش از انتظار طول می‌کشد

`getUpdates` long polling یک درخواست می‌سازد. timeout Bale را در options قرار دهید؛ پکیج برای timeout HTTP headroom مناسب اضافه می‌کند. loop، queue، backoff و offset persistence جزو این پکیج نیستند.

## فرض Telegram-only

شباهت نام‌ها به معنی سازگاری endpoint، field یا result نیست. [پوشش رسمی Bale](api-coverage.md) را بررسی کنید؛ API پشتیبانی‌نشده را با helper یا method خیالی جایگزین نکنید.
