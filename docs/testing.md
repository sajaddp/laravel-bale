---
title: تست Laravel Bale با Bale fake
description: راهنمای تست integration بله با Bale::fake و assertionهای Laravel-native بدون درخواست واقعی.
---

# تست Bale integration

`Bale::fake()` مسیر واقعی `BaleClient` را حفظ می‌کند: public methodها payload می‌سازند، HTTP Client Laravel درخواست را record می‌کند و parser پکیج envelope Bale را پردازش می‌کند. فقط درخواست‌های Bot API و دانلود فایل همین پکیج fake می‌شوند؛ HTTP نامرتبط برنامه نه fake است و نه در Bale assertionها شمرده می‌شود.

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

Array شرط subset strict است: همهٔ کلیدها و مقادیر supplied باید وجود داشته باشند، اما payload واقعی می‌تواند کلیدهای اضافی داشته باشد.

## assertion پیشرفته برای media

در صورت نیاز callback مستقیماً `Illuminate\Http\Client\Request` را می‌گیرد:

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

## Convenience، فایل و askReview

assertionها endpoint رسمی را مشاهده می‌کنند، نه نام convenience method:

~~~php
Bale::fake();

Bale::replyToMessage($message, 'پاسخ');
Bale::assertSent('sendMessage');

$binary = Bale::downloadFile('file-id');
Bale::assertSent('getFile');

Bale::askReview(userId: 123456789, delaySeconds: 30);
Bale::assertSent('askReview', ['delay_seconds' => 30]);
~~~

`Bale::assertNothingSent()` فقط وقتی fail می‌شود که این پکیج یک درخواست Bot API یا دانلود فایل ارسال کرده باشد.

## همراهی با Laravel `Http::fake()`

fakeهای URL-specific برای HTTP خارجی با `Bale::fake()` تداخلی ندارند و می‌توانند پیش یا پس از آن ثبت شوند، مشروط بر اینکه URLهای Bale را match نکنند:

~~~php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://example.test/*' => Http::response(['ok' => true]),
]);

Bale::fake();
~~~

اما اگر از catch-all `Http::fake()` (از جمله فراخوانی بدون آرگومان) یا الگویی مانند `'*'` استفاده می‌کنید، ابتدا `Bale::fake()` را ثبت کنید:

~~~php
Bale::fake();

Http::fake([
    '*' => Http::response('external fake'),
]);
~~~

Laravel callbackهای fake را به‌ترتیب ثبت بررسی می‌کند؛ بنابراین fake محدود Bale باید نخستین فرصت را برای پاسخ به URLهای Bale داشته باشد. این یک قاعدهٔ ترتیب fake در Laravel است، نه رفتار پروتکل Bale.

## تست خطاهای low-level

برای آزمایش envelope خطای مشخص، پاسخ malformed یا خطای HTTP، از Laravel `Http::fake()` مستقیم استفاده کنید. این روش برای assertionهای سطح پایین مناسب است؛ برای رفتار معمول integration، `Bale::fake()` خواناتر است.

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
