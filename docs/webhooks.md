---
title: Webhook بله در Laravel 13
description: راهنمای راه‌اندازی Webhook بازوی بله در Laravel 13 با مسیر دریافت Update، CSRF و Laravel Bale.
---

# چطور Webhook بله را در Laravel 13 تنظیم کنیم؟

Laravel Bale فقط URL خروجی Bale را با `setWebhook` تنظیم می‌کند. دریافت HTTP request، route، controller، اعتبارسنجی برنامه و پردازش Update مسئولیت برنامهٔ Laravel مصرف‌کننده است.

## یک route برای دریافت Update بسازید

در `routes/web.php` یک endpoint عمومی برای POSTهای Bale تعریف کنید:

~~~php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/bale/webhook', function (Request $request) {
    $update = $request->all();

    // Update را در application خودتان dispatch یا پردازش کنید.

    return response()->noContent();
});
~~~

## CSRF را فقط برای همان URI خارج کنید

POST خارجی Bale توکن CSRF مرورگر Laravel ندارد. در `bootstrap/app.php` دقیقاً همان URI route را از اعتبارسنجی CSRF خارج کنید:

~~~php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'bale/webhook',
    ]);
})
~~~

URI در این فهرست باید با route یکی باشد؛ در این مثال `bale/webhook` است، نه URL کامل.

## URL عمومی HTTPS را در Bale ثبت کنید

پس از deploy برنامه، URL عمومی و HTTPS همان endpoint را ثبت کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::setWebhook('https://example.test/bale/webhook');

$info = Bale::getWebhookInfo();

// در صورت نیاز به polling یا جایگزینی endpoint:
Bale::deleteWebhook();
~~~

`setWebhook` URL را برای تحویل Update به Bale می‌دهد؛ URL باید دقیقاً با route برنامه و HTTPS عمومی آن مطابقت داشته باشد.

## مرزهای پکیج و برنامه

این پکیج webhook secret، middleware سفارشی، `WebhookController`، route یا callback router فراهم نمی‌کند. این‌ها را از API Telegram حدس نزنید. طراحی احراز هویت، rate limiting، dispatch به queue و پاسخ‌گویی application در برنامهٔ Laravel شما قرار دارد.

اگر با 419 یا URL نادرست روبه‌رو شدید، [رفع اشکال](troubleshooting.md) را ببینید. برای دریافت pull-based به‌جای webhook، [راهنمای Polling](polling.md) را بخوانید.
