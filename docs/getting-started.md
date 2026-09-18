---
title: شروع کار با لاراول بله در لاراول ۱۳
description: راهنمای نصب، تنظیم توکن بازو و ارسال نخستین پیام بله با لاراول بله.
---

# شروع کار با لاراول بله

## نیازمندی‌ها

لاراول بله فقط لاراول ۱۳ و پی‌اچ‌پی ۸٫۳ یا جدیدتر را پشتیبانی می‌کند.

## نصب

~~~shell
composer require sajaddp/laravel-bale
~~~

## پیکربندی

توکن بازو را در محیط برنامه قرار دهید:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

در صورت نیاز تنظیم را منتشر کنید:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

سپس کلاس دسترسی را وارد کنید:

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

`replyToMessage` متد کمکی پکیج است. دادهٔ خام پیام باید `message_id` و `chat.id` را به‌صورت عدد صحیح داشته باشد؛ در غیر این صورت پیش از ارسال درخواست اچ‌تی‌تی‌پی، `InvalidArgumentException` رخ می‌دهد.

## خطاهای پایه

پاسخ معتبر بله با `ok: false` به `Sajaddp\Bale\Exceptions\BaleRequestException` تبدیل می‌شود. پاسخ ناموفق اچ‌تی‌تی‌پی یا ساختار پاسخ نامعتبر، رفتار معمول کارخواه اچ‌تی‌تی‌پی لاراول را حفظ می‌کند و ممکن است `Illuminate\Http\Client\RequestException` یا `UnexpectedValueException` بدهد.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

برای مثال‌های بعدی [دستورپخت‌ها](recipes.md)، برای حدود رابط برنامه‌نویسی [پوشش رسمی](api-coverage.md)، و برای آزمون بدون شبکه [راهنمای آزمون](testing.md) را ببینید.
