---
title: دستورپخت‌های لاراول بله
description: نمونه‌های کوتاه و واقعی لاراول بله برای پیام، رسانه، پاسخ انتخاب، دریافت دوره‌ای، وب‌هوک و آزمون.
---

# دستورپخت‌ها

همهٔ مثال‌ها از متدهای واقعی پکیج استفاده می‌کنند. برای امضاها به [مرجع رابط برنامه‌نویسی](api-reference.md) مراجعه کنید.

## ارسال پیام

~~~php
Bale::sendMessage(chatId: 123456789, text: 'سلام');
~~~

## پاسخ به پیام

~~~php
Bale::replyToMessage($update['message'], 'پاسخ شما');
~~~

## دکمه‌های درون‌خطی و پاسخ انتخاب

~~~php
Bale::sendMessage(
    chatId: 123456789,
    text: 'انتخاب کنید',
    options: ['reply_markup' => ['inline_keyboard' => [
        [['text' => 'تأیید', 'callback_data' => 'confirm']],
    ]]],
);

Bale::answerCallbackQuery(callbackQueryId: $update['callback_query']['id']);
~~~

## آپلود فایل محلی

~~~php
Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/report.pdf')),
);
~~~

## استفادهٔ دوباره از شناسهٔ فایل

~~~php
Bale::sendDocument(chatId: 123456789, document: 'bale-file-id');
~~~

## دانلود و ذخیرهٔ فایل

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put('bale/report.pdf', Bale::downloadFile($fileId));
~~~

## ارسال گروه رسانه

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

## دریافت دوره‌ای با `getUpdates`

~~~php
$updates = Bale::getUpdates(['offset' => $nextOffset, 'timeout' => 30]);
~~~

این فقط یک درخواست است؛ حلقه و ذخیرهٔ `offset` بر عهدهٔ برنامهٔ شماست.

## وب‌هوک در لاراول ۱۳

~~~php
// routes/web.php
Route::post('/bale/webhook', fn (\Illuminate\Http\Request $request) => response()->noContent());

// سپس در bootstrap/app.php همان URI را از CSRF مستثنا کنید.
Bale::setWebhook('https://example.test/bale/webhook');
~~~

برای جزئیات 419 به [رفع اشکال](troubleshooting.md) مراجعه کنید. پکیج مسیر یا کنترل‌کننده نمی‌سازد.

## `askReview`

~~~php
Bale::askReview(userId: 123456789, delaySeconds: 30);
~~~

## آزمون اتصال به بله

~~~php
Bale::fake();

Bale::sendMessage(chatId: 123456789, text: 'سلام');

Bale::assertSent('sendMessage', ['text' => 'سلام']);
~~~

جزئیات در [راهنمای آزمون](testing.md) است.
