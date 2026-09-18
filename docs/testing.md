---
title: آزمون لاراول بله با `Bale::fake()`
description: راهنمای آزمون اتصال به بله با `Bale::fake()` و بررسی‌های مبتنی بر لاراول، بدون درخواست واقعی.
---

# آزمون اتصال به بله

`Bale::fake()` مسیر واقعی `BaleClient` را حفظ می‌کند: متدهای عمومی دادهٔ ارسالی را می‌سازند، کارخواه اچ‌تی‌تی‌پی لاراول درخواست را ثبت می‌کند و تجزیه‌کنندهٔ پکیج ساختار پاسخ بله را پردازش می‌کند. فقط درخواست‌های رابط برنامه‌نویسی بازو و دانلود فایل همین پکیج شبیه‌سازی می‌شوند؛ درخواست‌های نامرتبط برنامه نه شبیه‌سازی می‌شوند و نه در بررسی‌های بله شمرده می‌شوند.

## شروع ساده

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::fake();

$order->confirm();

Bale::assertSent('sendMessage', [
    'chat_id' => 123456789,
    'text' => 'سفارش شما تأیید شد',
]);
Bale::assertSentTimes('sendMessage', 1);
Bale::assertNotSent('sendDocument');
~~~

اگر برای بررسی آرایه بدهید، همهٔ کلیدها و مقدارهای آن باید در دادهٔ ارسالی یکسان باشند؛ وجود کلیدهای اضافه در درخواست واقعی مشکلی ایجاد نمی‌کند.

## بررسی پیشرفتهٔ رسانه

در صورت نیاز، تابع بررسی مستقیماً `Illuminate\Http\Client\Request` را می‌گیرد:

~~~php
Bale::fake();

Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/invoice.pdf')),
);

Bale::assertSent(
    'sendDocument',
    fn (\Illuminate\Http\Client\Request $request) => $request->isMultipart(),
);
~~~

## متدهای کمکی، فایل و `askReview`

بررسی‌ها متد رسمی را می‌بینند، نه نام متد کمکی:

~~~php
Bale::fake();

Bale::replyToMessage($message, 'پاسخ');
Bale::assertSent('sendMessage');

$binary = Bale::downloadFile('file-id');
Bale::assertSent('getFile');

Bale::askReview(userId: 123456789, delaySeconds: 30);
Bale::assertSent('askReview', ['delay_seconds' => 30]);
~~~

`Bale::assertNothingSent()` فقط وقتی خطا می‌دهد که این پکیج درخواست رابط برنامه‌نویسی بازو یا دانلود فایل فرستاده باشد.

## همراهی با لاراول و `Http::fake()`

شبیه‌سازی‌های نشانی‌محور برای درخواست‌های خارجی با `Bale::fake()` تداخلی ندارند و می‌توانند پیش یا پس از آن ثبت شوند، به‌شرطی که با نشانی‌های بله منطبق نباشند:

~~~php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://example.test/*' => Http::response(['ok' => true]),
]);

Bale::fake();
~~~

اما اگر از `Http::fake()` فراگیر، از جمله فراخوانی بدون آرگومان یا الگویی مانند `'*'`، استفاده می‌کنید، ابتدا `Bale::fake()` را ثبت کنید:

~~~php
Bale::fake();

Http::fake([
    '*' => Http::response('external fake'),
]);
~~~

لاراول تابع‌های شبیه‌سازی را به‌ترتیب ثبت بررسی می‌کند؛ بنابراین شبیه‌سازی محدود بله باید نخستین فرصت را برای پاسخ به نشانی‌های بله داشته باشد. این قاعدهٔ ترتیب شبیه‌سازی در لاراول است، نه رفتار پروتکل بله.

## تست خطاهای سطح پایین

برای آزمایش ساختار خطای مشخص، پاسخ بدساخت یا خطای اچ‌تی‌تی‌پی، مستقیماً از لاراول و `Http::fake()` استفاده کنید. این روش برای بررسی‌های سطح پایین مناسب است؛ برای رفتار معمول اتصال، `Bale::fake()` خواناتر است.

~~~php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://tapi.bale.ai/bot*/sendMessage' => Http::response([
        'ok' => false,
        'error_code' => 400,
        'description' => 'Bale rejected the request.',
    ], 400),
]);
~~~
