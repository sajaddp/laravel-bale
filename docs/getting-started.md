---
title: شروع Laravel Bale در Laravel 13
description: راهنمای نصب آینده، تنظیم BALE_BOT_TOKEN و ارسال نخستین پیام بله با Laravel Bale.
---

# شروع کار با Laravel Bale

## نیازمندی‌ها

Laravel Bale فقط Laravel 13 و PHP 8.3 یا جدیدتر را پشتیبانی می‌کند. پکیج هنوز در Packagist منتشر نشده است؛ بنابراین `composer require sajaddp/laravel-bale` دستور مورد انتظار پس از انتشار است، نه یک نصب عمومیِ فعال در حال حاضر.

## پیکربندی

توکن بازو را در محیط برنامه قرار دهید:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

در صورت نیاز تنظیم را منتشر کنید:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

سپس Facade را import کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;
~~~

## نخستین پیام و پاسخ

~~~php
Bale::sendMessage(chatId: 123456789, text: 'سلام');

Bale::replyToMessage(
    message: $update['message'],
    text: 'پاسخ شما',
);
~~~

`replyToMessage` یک convenience method است. raw Message باید `message_id` و `chat.id` از نوع integer داشته باشد؛ در غیر این صورت پیش از ارسال HTTP، `InvalidArgumentException` رخ می‌دهد.

## خطاهای پایه

پاسخ معتبر Bale با `ok: false` به `Sajaddp\Bale\Exceptions\BaleRequestException` تبدیل می‌شود. پاسخ HTTP ناموفق یا envelope نامعتبر، semantics خود Laravel HTTP Client را حفظ می‌کند و می‌تواند `Illuminate\Http\Client\RequestException` یا `UnexpectedValueException` بدهد.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

برای مثال‌های بعدی [دستورپخت‌ها](recipes.md)، برای حدود API [پوشش رسمی](api-coverage.md)، و برای تست بدون شبکه [راهنمای تست](testing.md) را ببینید.
