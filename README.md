# Laravel Bale — پکیج Laravel 13 برای Bale Bot API

[![Tests](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml/badge.svg)](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml)

A minimal Laravel 13 integration for the Bale Bot API.

`sajaddp/laravel-bale` یک کلاینت کم‌حجم برای فراخوانی Bale Bot API در Laravel 13 است. این پکیج PHP 8.3+ را پشتیبانی می‌کند، پاسخ‌های مستند بله را به آرایه یا مقدار نتیجه تبدیل می‌کند و عمداً یک bot framework نیست.

## چرا Laravel Bale؟

برای کارهای رایج مانند ارسال پیام، رسانه، Webhook، Polling، Callback Query و فایل، از Facade و Laravel HTTP Client استفاده می‌کنید؛ بدون route، worker، DTO یا state machine پنهان. رفتار Bale همیشه از مستندات رسمی بله می‌آید، نه از فرض سازگاری با Telegram.

## نیازمندی‌ها

- Laravel 13
- PHP 8.3 یا جدیدتر

## نصب و تنظیم `BALE_BOT_TOKEN`

این پکیج در زمان نگارش هنوز در Packagist منتشر نشده است؛ پس فرمان زیر دستور نصب موردنظر پس از انتشار است، نه یک نصب عمومیِ فعال در حال حاضر:

~~~shell
composer require sajaddp/laravel-bale
~~~

پس از نصب، Laravel provider را خودکار کشف می‌کند. توکن بازو را در محیط برنامه قرار دهید:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

در صورت نیاز، تنها تنظیم واقعی پکیج را منتشر کنید:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

Facade را صریحاً import کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;
~~~

## راهنمای عمیق‌تر

README برای شروع سریع است. مستندات ساخت‌یافتهٔ مخزن جزئیات و مرجع کامل را نگه می‌دارند:

- [مستندات آنلاین](https://sajaddehshiri.ir/laravel-bale/)
- [شروع کار](docs/getting-started.md)
- [پیام‌ها](docs/messages.md)
- [Webhook](docs/webhooks.md)
- [Polling](docs/polling.md)
- [فایل و رسانه](docs/files-media.md)
- [Callback و Keyboard](docs/callbacks-keyboards.md)
- [مرجع API](docs/api-reference.md)
- [پوشش Bale Bot API](docs/api-coverage.md)
- [تست](docs/testing.md)
- [دستورپخت‌ها](docs/recipes.md)
- [رفع اشکال](docs/troubleshooting.md)
- [AI Coding](docs/ai-coding.md)

## شروع سریع: چطور با Laravel به بله پیام بفرستیم؟

~~~php
use Sajaddp\Bale\Facades\Bale;

$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
);
~~~

آرگومان‌های الزامی هر متد بر کلید همنام در `options` مقدم‌اند. برای `options` فقط فیلدهایی را بفرستید که Bale برای همان endpoint مستند کرده است.

## متدهای رسمی Bale Bot API که پشتیبانی می‌شوند

این جدول از public API فعلی `BaleClient` ساخته شده است. همهٔ موارد این بخش wrapper مستقیم یک روش رسمی Bale Bot API هستند، نه یک endpoint خیالی.

| گروه | Package method | کاربرد | خروجی |
| --- | --- | --- | --- |
| Bot | `getMe` | اطلاعات بازو | `array` |
| Messaging | `sendMessage` | ارسال متن | `array` |
| Messaging | `forwardMessage` / `copyMessage` | فوروارد یا کپی پیام | `array` |
| Messaging | `sendChatAction` | نمایش action در گفتگو | `bool` |
| Webhook | `setWebhook` / `deleteWebhook` | ثبت یا حذف Webhook خروجی | `bool` |
| Webhook | `getWebhookInfo` | اطلاعات Webhook | `array` |
| Updates | `getUpdates` | دریافت یک‌بارهٔ Updateها | `array` |
| Callbacks | `answerCallbackQuery` | پاسخ به Callback Query | `bool` |
| Review | `askReview` | درخواست ثبت یا ویرایش نظر دربارهٔ بازو | `bool` |
| Message operations | `editMessageText` / `editMessageCaption` / `editMessageReplyMarkup` | ویرایش پیام یا Inline Keyboard | نتیجهٔ خام Bale |
| Message operations | `deleteMessage` | حذف پیام | `bool` |
| Media / files | `sendPhoto` / `sendAudio` / `sendDocument` / `sendVideo` / `sendAnimation` / `sendVoice` | ارسال یک رسانه | `array` |
| Media / files | `sendMediaGroup` | ارسال گروه رسانه | `array` |
| Media / files | `getFile` | دریافت metadata فایل | `array` |
| Location / contact | `sendLocation` / `sendContact` | ارسال موقعیت یا مخاطب | `array` |

## امکانات اضافهٔ Laravel Bale

دو متد زیر endpoint مستقیم Bale نیستند. آن‌ها فقط workflowهای تکراری را با APIهای رسمی بالا compose می‌کنند؛ برای AI coding agent نیز این تمایز مهم است.

| Laravel Bale convenience method | بر پایهٔ API رسمی | خروجی |
| --- | --- | --- |
| `replyToMessage` | `sendMessage` و `reply_to_message_id` | `array` |
| `downloadFile` | `getFile` و دانلود فایل مستندشدهٔ Bale | `string` binary |

## درخواست نظر با `askReview`

`askReview` یک wrapper رسمی Bale Bot API است، نه helper پکیج. طبق مستندات رسمی، برای نمایش فرم ثبت یا ویرایش نظر دربارهٔ بازو به کار می‌رود؛ نمایش نهایی آن به پشتیبانی نسخهٔ Bale client و شرایط کاربر بستگی دارد.

~~~php
Bale::askReview(
    userId: 123456789,
    delaySeconds: 30,
);
~~~

هر دو مقدار integer و الزامی‌اند. `delaySeconds` تأخیر نمایش فرم از زمان فراخوانی را برحسب ثانیه تعیین می‌کند و پاسخ موفق Bale برابر `true` است.

## تست ربات بله در Laravel بدون درخواست واقعی

برای تست integration لازم نیست URLهای Bale، envelope پاسخ یا جزئیات `Http::fake()` را بدانید:

~~~php
Bale::fake();

$order->confirm();

Bale::assertSent('sendMessage', [
    'chat_id' => 123456789,
    'text' => 'سفارش شما تأیید شد',
]);
~~~

`Bale::fake()` فقط درخواست‌های Bot API و دانلود فایلِ همین پکیج را fake می‌کند؛ HTTP نامرتبط برنامه را جعل یا جزو assertionها حساب نمی‌کند. API تست عبارت است از `Bale::fake()`، `Bale::assertSent()`، `Bale::assertSentTimes()`، `Bale::assertNotSent()` و `Bale::assertNothingSent()`. assertionها endpoint رسمی مانند `sendMessage` را می‌بینند، نه convenience methodهایی مانند `replyToMessage`. اگر در همان تست از catch-all `Http::fake()` استفاده می‌کنید، ابتدا `Bale::fake()` را ثبت کنید؛ جزئیات، مثال پیشرفته‌تر و تست رسانه در [راهنمای تست](docs/testing.md) آمده است.

## چطور به یک پیام بله پاسخ بدهیم؟

روش low-level رسمی، `sendMessage` با `reply_to_message_id` است:

~~~php
Bale::sendMessage(
    chatId: $message['chat']['id'],
    text: 'پاسخ شما',
    options: [
        'reply_to_message_id' => $message['message_id'],
    ],
);
~~~

برای این workflow تکراری از convenience method پکیج استفاده کنید:

~~~php
Bale::replyToMessage(
    message: $message,
    text: 'پاسخ شما',
    options: [
        'reply_markup' => ['inline_keyboard' => []],
    ],
);
~~~

این متد فقط `message_id` صحیح و `chat.id` صحیح را از raw Bale Message array می‌خواند؛ هر دو باید integer باشند. سپس `sendMessage` را فراخوانی می‌کند. `chat_id`، `text` و `reply_to_message_id` از خود workflow می‌آیند و با `options` قابل جایگزینی نیستند. شکل کامل Message اعتبارسنجی یا DTO نمی‌شود؛ ورودی ناقص پیش از هر درخواست HTTP با `InvalidArgumentException` رد می‌شود.

## چطور دکمه Inline Keyboard و Callback Query بسازیم؟

ساختار array مستند Bale را مستقیماً به `reply_markup` بدهید؛ keyboard builder لازم نیست:

~~~php
Bale::sendMessage(
    chatId: 123456789,
    text: 'یک گزینه را انتخاب کنید',
    options: [
        'reply_markup' => [
            'inline_keyboard' => [
                [
                    ['text' => 'تأیید', 'callback_data' => 'confirm'],
                    ['text' => 'وب‌سایت', 'url' => 'https://example.test'],
                ],
            ],
        ],
    ],
);
~~~

هنگام دریافت `callback_query`، ابتدا query را پاسخ دهید. فیلد `message` در CallbackQuery می‌تواند موجود نباشد؛ فقط در صورت وجود، پیام را ویرایش کنید:

~~~php
$callback = $update['callback_query'];

Bale::answerCallbackQuery(callbackQueryId: $callback['id']);

if (isset($callback['message'])) {
    Bale::editMessageText(
        chatId: $callback['message']['chat']['id'],
        messageId: $callback['message']['message_id'],
        text: 'ثبت شد',
    );
}
~~~

این دو عملیات مستقل‌اند؛ پکیج helper ترکیبی callback+edit ندارد.

## ارسال فایل و رسانه: `file_id`، URL و آپلود محلی

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendAnimation` و `sendVoice`:

- `string` بدون تغییر به Bale می‌رود و باید یک Bale `file_id` یا HTTP URL باشد.
- `SplFileInfo` یعنی آپلود صریح فایل محلی با `multipart/form-data`.
- رشتهٔ مسیر فایل محلی به‌صورت خودکار upload نمی‌شود.

~~~php
use SplFileInfo;

Bale::sendDocument(
    chatId: 123456789,
    document: 'bale-file-id',
    options: ['caption' => 'استفادهٔ مجدد از فایل'],
);

Bale::sendVideo(
    chatId: 123456789,
    video: 'https://example.test/video.mp4',
);

Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/example.pdf')),
    options: ['caption' => 'آپلود محلی'],
);
~~~

مستندات فعلی Bale برای `sendPhoto`، پارامتر `from_chat_id` را نیز الزامی می‌داند:

~~~php
Bale::sendPhoto(
    chatId: '@target_channel',
    fromChatId: '@source_channel',
    photo: new SplFileInfo(storage_path('app/example.jpg')),
);
~~~

برای album از `sendMediaGroup` و array مستند Bale استفاده کنید. attachment محلی باید نام صریح و `attach://name` متناظر داشته باشد.

## چطور فایل بله را دانلود کنیم؟

`getFile($fileId)` یک درخواست رسمی برای metadata است. `downloadFile($fileId)` convenience method پکیج است که نخست `getFile` را اجرا می‌کند، `file_path` غیرخالی را می‌گیرد و محتوای binary را برمی‌گرداند:

~~~php
$contents = Bale::downloadFile($fileId);
~~~

در برنامهٔ Laravel خودتان می‌توانید آن را با Storage compose کنید؛ پکیج به Filesystem وابسته نیست:

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

طبق مستندات رسمی فعلی Bale، بازوها تا ۲۰ مگابایت فایل دانلود می‌کنند و لینک دانلود حاصل از `getFile` برای یک ساعت تضمین‌شده است؛ با فراخوانی دوبارهٔ `getFile` لینک تازه می‌شود. پکیج این محدودیت را محلی enforce نمی‌کند، URL توکن‌دار را public نمی‌کند و فایل را تبدیل یا بررسی MIME نمی‌کند. اگر `file_path` معتبر نباشد، `UnexpectedValueException` پیش از درخواست دانلود رخ می‌دهد.

## دریافت Update با Polling

`getUpdates` دقیقاً یک درخواست می‌سازد؛ loop، queue و نگه‌داری offset با برنامهٔ شماست:

~~~php
$updates = Bale::getUpdates([
    'offset' => $nextOffset,
    'limit' => 100,
    'timeout' => 30,
]);

// پس از پردازش، offset بعدی را در storage برنامهٔ خودتان نگه دارید.
~~~

برای timeout صحیح integer، پکیج پنج ثانیه headroom انتقال HTTP در نظر می‌گیرد (حداقل ۳۰ ثانیه) بدون تغییر payload Bale.

## راه‌اندازی Webhook در Laravel

یک راه کامل Laravel 13 این است که route را در `routes/web.php` نگه دارید و فقط همان URI را از CSRF خارج کنید. route زیر دقیقاً این URL عمومی را می‌سازد: `https://example.test/bale/webhook`.

~~~php
// routes/web.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/bale/webhook', function (Request $request) {
    $update = $request->all();

    // application logic
});
~~~

چون `routes/web.php` در middleware گروه `web` قرار دارد، POST خارجی Bale CSRF token ندارد. در `bootstrap/app.php`، callback موجود `withMiddleware` را این‌گونه کامل کنید تا فقط همان مسیر مستثنا شود:

~~~php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->preventRequestForgery(except: [
        'bale/webhook',
    ]);
})
~~~

حالا URL ثبت‌شده در Bale دقیقاً با route یکی است:

~~~php
Bale::setWebhook('https://example.test/bale/webhook');
~~~

برای مشاهده یا حذف تنظیمات خروجی از `getWebhookInfo()` و `deleteWebhook()` استفاده کنید. پکیج webhook router، controller، middleware یا secret validation اختراع نمی‌کند.

## ویرایش، کپی، فوروارد و حذف پیام

~~~php
Bale::forwardMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::copyMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::sendChatAction(chatId: 123456789, action: 'upload_photo');

Bale::editMessageText(chatId: 123456789, messageId: 42, text: 'متن جدید');
Bale::editMessageCaption(chatId: 123456789, messageId: 42, options: ['caption' => 'زیرنویس']);
Bale::editMessageReplyMarkup(chatId: 123456789, messageId: 42, options: ['reply_markup' => $replyMarkup]);
Bale::deleteMessage(chatId: 123456789, messageId: 42);
~~~

## خطاهای `BaleRequestException` و `RequestException` چه تفاوتی دارند؟

- پاسخ معتبر Bale با `ok: false`، `Sajaddp\Bale\Exceptions\BaleRequestException` می‌دهد.
- HTTP ناموفق خارج از envelope معتبر Bale، از جمله دانلود binary ناموفق، semantics خود Laravel HTTP Client یعنی `Illuminate\Http\Client\RequestException` را حفظ می‌کند.
- پاسخ موفقِ malformed یا نتیجهٔ با شکل نامعتبر، `UnexpectedValueException` می‌دهد.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

## تفاوت Bale و Telegram Bot API

Bale از نظر نام‌گذاری به Telegram Bot API شباهت دارد، اما این پکیج فقط مستندات Bale را دنبال می‌کند. method، option یا رفتار Telegram-only را فرض نکنید. برای هر کار از API پشتیبانی‌شدهٔ پکیج و مستندات رسمی Bale استفاده کنید.

## Laravel Boost و AI Coding

پکیج یک Boost guideline و Skill با نام `bale-development` دارد. در اپلیکیشن مصرف‌کننده، بعد از نصب Laravel Boost اجرا کنید:

~~~shell
php artisan boost:install
~~~

این Skill به AI coding agent کمک می‌کند API واقعی پکیج، تفاوت wrapper رسمی و convenience method، رسانه، دانلود فایل، callback و مرز webhook/polling را درست استفاده کند.

## چه چیزهایی عمداً در این پکیج نیست؟

- webhook framework، polling daemon یا Artisan polling command
- command/handler system، conversation state یا DTOهای Message / Update / Chat
- keyboard builder و helper ترکیبی callback+edit
- تشخیص خودکار مسیر فایل محلی
- public file URL حاوی bot token
- `Storage` abstraction یا helper اختصاصی برای ذخیرهٔ فایل

این مرزها intentional هستند: Laravel Bale یک API client باقی می‌ماند، نه یک framework.

## پرسش‌های متداول

### آیا این پکیج برای Laravel 12 کار می‌کند؟

خیر. محدودهٔ پشتیبانی پکیج Laravel 13 است.

### آیا رشتهٔ مسیر فایل به‌صورت خودکار آپلود می‌شود؟

خیر. `string` فقط `file_id` یا HTTP URL است؛ برای آپلود محلی از `SplFileInfo` استفاده کنید.

### آیا پکیج Webhook را خودش می‌سازد؟

خیر. `setWebhook` فقط URL خروجی Bale را پیکربندی می‌کند. route دریافت‌کننده را در اپلیکیشن Laravel خودتان می‌نویسید.

### آیا `getUpdates` خودش Loop اجرا می‌کند؟

خیر؛ یک درخواست می‌فرستد. اجرای loop و نگه‌داری offset با برنامهٔ شماست.

### چطور فایل دریافتی را ذخیره کنم؟

در اپلیکیشن Laravel خودتان، `Storage::put('path', Bale::downloadFile($fileId))` را استفاده کنید. پکیج Filesystem dependency ندارد.

### آیا API بله دقیقاً همان Telegram Bot API است؟

خیر. شباهت نام‌ها مجوز فرض‌کردن endpoint یا option نیست؛ مستندات Bale مرجع است.

## توسعهٔ پکیج

~~~shell
composer install
composer test
~~~

تست‌ها با Laravel HTTP fake اجرا می‌شوند و نباید درخواست واقعی Bale یا token واقعی بسازند.
