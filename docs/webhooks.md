---
title: وب‌هوک بله در Laravel 13
description: راهنمای راه‌اندازی وب‌هوک بازوی بله در Laravel 13، با مسیر دریافت Update، CSRF و Laravel Bale.
---

# چطور وب‌هوک بله را در Laravel 13 تنظیم کنیم؟

Laravel Bale فقط نشانی دریافتی Bale را با `setWebhook` تنظیم می‌کند. دریافت درخواست HTTP، مسیر، کنترل‌کننده، اعتبارسنجی برنامه و پردازش Update بر عهدهٔ برنامهٔ Laravel استفاده‌کننده است.

## یک مسیر برای دریافت Update بسازید

در `routes/web.php` یک مسیر عمومی برای POSTهای Bale تعریف کنید:

~~~php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/bale/webhook', function (Request $request) {
    $update = $request->all();

    // Update را در برنامهٔ خودتان ارسال یا پردازش کنید.

    return response()->noContent();
});
~~~

## CSRF را فقط برای همان نشانی خارج کنید

POST خارجی Bale توکن CSRF مرورگر Laravel را ندارد. در `bootstrap/app.php` دقیقاً همان نشانی مسیر را از اعتبارسنجی CSRF خارج کنید:

~~~php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'bale/webhook',
    ]);
})
~~~

نشانی در این فهرست باید با مسیر یکی باشد؛ در این مثال `bale/webhook` است، نه URL کامل.

## URL عمومی HTTPS را در Bale ثبت کنید

پس از استقرار برنامه، URL عمومی HTTPS همان مسیر را ثبت کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::setWebhook('https://example.test/bale/webhook');

$info = Bale::getWebhookInfo();

// در صورت نیاز به دریافت دوره‌ای یا جایگزینی نشانی:
Bale::deleteWebhook();
~~~

`setWebhook` URL را برای تحویل Update به Bale می‌دهد؛ URL باید دقیقاً با مسیر برنامه و HTTPS عمومی آن مطابقت داشته باشد.

## مرزهای پکیج و برنامه

این پکیج راز وب‌هوک، میان‌افزار سفارشی، `WebhookController`، مسیر یا مسیردهی Callback فراهم نمی‌کند. این موارد را از API Telegram حدس نزنید. طراحی احراز هویت، محدودکردن نرخ، فرستادن به صف و پاسخ‌گویی در برنامهٔ Laravel شما قرار دارد.

اگر با 419 یا URL نادرست روبه‌رو شدید، [رفع اشکال](troubleshooting.md) را ببینید. برای دریافت دوره‌ای به‌جای وب‌هوک، [راهنمای دریافت دوره‌ای](polling.md) را بخوانید.
