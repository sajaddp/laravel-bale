---
title: تست Laravel Bale با Bale::fake()
description: راهنمای تست اتصال به بله با Bale::fake() و بررسی‌های مبتنی بر Laravel، بدون درخواست واقعی.
---

# تست اتصال به Bale

`Bale::fake()` مسیر واقعی `BaleClient` را حفظ می‌کند: متدهای عمومی دادهٔ ارسالی را می‌سازند، Laravel HTTP Client درخواست را ثبت می‌کند و تجزیه‌کنندهٔ پکیج ساختار پاسخ Bale را پردازش می‌کند. فقط درخواست‌های Bot API و دانلود فایل همین پکیج شبیه‌سازی می‌شوند؛ درخواست‌های HTTP نامرتبط برنامه نه شبیه‌سازی می‌شوند و نه در بررسی‌های Bale شمرده می‌شوند.

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

## متدهای کمکی، فایل و askReview

بررسی‌ها متد رسمی API را می‌بینند، نه نام متد کمکی:

~~~php
Bale::fake();

Bale::replyToMessage($message, 'پاسخ');
Bale::assertSent('sendMessage');

$binary = Bale::downloadFile('file-id');
Bale::assertSent('getFile');

Bale::askReview(userId: 123456789, delaySeconds: 30);
Bale::assertSent('askReview', ['delay_seconds' => 30]);
~~~

`Bale::assertNothingSent()` فقط وقتی خطا می‌دهد که این پکیج درخواست Bot API یا دانلود فایل فرستاده باشد.

## همراهی با Laravel `Http::fake()`

شبیه‌سازی‌های نشانی‌محور برای HTTP خارجی با `Bale::fake()` تداخلی ندارند و می‌توانند پیش یا پس از آن ثبت شوند، به‌شرطی که با URLهای Bale منطبق نباشند:

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

Laravel تابع‌های شبیه‌سازی را به‌ترتیب ثبت بررسی می‌کند؛ بنابراین شبیه‌سازی محدود Bale باید نخستین فرصت را برای پاسخ به URLهای Bale داشته باشد. این قاعدهٔ ترتیب شبیه‌سازی در Laravel است، نه رفتار پروتکل Bale.

## تست خطاهای سطح پایین

برای آزمایش ساختار خطای مشخص، پاسخ بدساخت یا خطای HTTP، مستقیماً از Laravel `Http::fake()` استفاده کنید. این روش برای بررسی‌های سطح پایین مناسب است؛ برای رفتار معمول اتصال، `Bale::fake()` خواناتر است.

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
