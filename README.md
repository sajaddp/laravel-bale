# Laravel Bale — پکیج Laravel 13 برای Bale Bot API

[![Tests](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml/badge.svg)](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml)

پکیجی کم‌حجم برای کار با Bale Bot API در Laravel 13.

`sajaddp/laravel-bale` پکیجی کم‌حجم برای کار با Bale Bot API در Laravel 13 است. این پکیج PHP 8.3+ را پشتیبانی می‌کند، پاسخ‌های مستند بله را به آرایه یا مقدار نتیجه تبدیل می‌کند و عمداً چارچوب ساخت ربات نیست.

## چرا Laravel Bale؟

برای کارهای رایج مانند ارسال پیام، رسانه، وب‌هوک، دریافت دوره‌ای، Callback Query و فایل، از Facade و Laravel HTTP Client استفاده می‌کنید؛ بدون مسیر، پردازشگر، DTO یا وضعیت پنهان. رفتار Bale همیشه از مستندات رسمی بله می‌آید، نه از فرض سازگاری با Telegram.

## نیازمندی‌ها

- Laravel 13
- PHP 8.3 یا جدیدتر

## نصب و تنظیم `BALE_BOT_TOKEN`

این پکیج در زمان نگارش هنوز در Packagist منتشر نشده است؛ پس فرمان زیر دستور نصب موردنظر پس از انتشار است، نه یک نصب عمومیِ فعال در حال حاضر:

~~~shell
composer require sajaddp/laravel-bale
~~~

پس از نصب، Laravel ارائه‌دهندهٔ پکیج را خودکار پیدا می‌کند. توکن بازو را در محیط برنامه قرار دهید:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

در صورت نیاز، تنها تنظیم واقعی پکیج را منتشر کنید:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

کلاس Facade را صریحاً وارد کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;
~~~

## راهنمای عمیق‌تر

README برای شروع سریع است. مستندات ساخت‌یافتهٔ مخزن جزئیات و مرجع کامل را نگه می‌دارند:

- [مستندات آنلاین](https://sajaddehshiri.ir/laravel-bale/)
- [شروع کار](docs/getting-started.md)
- [پیام‌ها](docs/messages.md)
- [وب‌هوک](docs/webhooks.md)
- [دریافت دوره‌ای](docs/polling.md)
- [فایل و رسانه](docs/files-media.md)
- [Callback و دکمه‌ها](docs/callbacks-keyboards.md)
- [مرجع API](docs/api-reference.md)
- [پوشش Bale Bot API](docs/api-coverage.md)
- [تست](docs/testing.md)
- [دستورپخت‌ها](docs/recipes.md)
- [رفع اشکال](docs/troubleshooting.md)
- [راهنمای هوش مصنوعی](docs/ai-coding.md)

## شروع سریع: چطور با Laravel به بله پیام بفرستیم؟

~~~php
use Sajaddp\Bale\Facades\Bale;

$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
);
~~~

آرگومان‌های الزامی هر متد بر کلید همنام در `options` مقدم‌اند. برای `options` فقط فیلدهایی را بفرستید که Bale برای همان متد API مستند کرده است.

## متدهای رسمی Bale Bot API که پشتیبانی می‌شوند

این جدول از API عمومی فعلی `BaleClient` ساخته شده است. همهٔ موارد این بخش متدهای مستقیمِ رسمی Bale Bot API هستند، نه متدی خیالی.

| گروه | متد پکیج | کاربرد | خروجی |
| --- | --- | --- | --- |
| بازو | `getMe` | اطلاعات بازو | `array` |
| پیام‌ها | `sendMessage` | ارسال متن | `array` |
| پیام‌ها | `forwardMessage` / `copyMessage` | فوروارد یا کپی پیام | `array` |
| پیام‌ها | `sendChatAction` | نمایش وضعیت در گفتگو | `bool` |
| وب‌هوک | `setWebhook` / `deleteWebhook` | ثبت یا حذف وب‌هوک خروجی | `bool` |
| وب‌هوک | `getWebhookInfo` | اطلاعات وب‌هوک | `array` |
| Updateها | `getUpdates` | دریافت یک‌بارهٔ Updateها | `array` |
| Callback | `answerCallbackQuery` | پاسخ به Callback Query | `bool` |
| نظر | `askReview` | درخواست ثبت یا ویرایش نظر دربارهٔ بازو | `bool` |
| کار با پیام | `editMessageText` / `editMessageCaption` / `editMessageReplyMarkup` | ویرایش پیام یا دکمه‌های درون‌خطی | نتیجهٔ خام Bale |
| کار با پیام | `deleteMessage` | حذف پیام | `bool` |
| رسانه و فایل | `sendPhoto` / `sendAudio` / `sendDocument` / `sendVideo` / `sendAnimation` / `sendVoice` | ارسال یک رسانه | `array` |
| رسانه و فایل | `sendMediaGroup` | ارسال گروه رسانه | `array` |
| رسانه و فایل | `getFile` | دریافت اطلاعات فایل | `array` |
| موقعیت و مخاطب | `sendLocation` / `sendContact` | ارسال موقعیت یا مخاطب | `array` |

## امکانات اضافهٔ Laravel Bale

دو متد زیر مستقیماً متد API Bale نیستند. آن‌ها فقط روندهای تکراری را با APIهای رسمی بالا ساده می‌کنند؛ این تمایز برای ابزارهای هوش مصنوعی هم مهم است.

| متد کمکی Laravel Bale | بر پایهٔ API رسمی | خروجی |
| --- | --- | --- |
| `replyToMessage` | `sendMessage` و `reply_to_message_id` | `array` |
| `downloadFile` | `getFile` و دانلود فایل مستندشدهٔ Bale | محتوای دودویی `string` |

## درخواست نظر با `askReview`

`askReview` متد رسمی Bale Bot API است، نه متد کمکی پکیج. طبق مستندات رسمی، برای نمایش فرم ثبت یا ویرایش نظر دربارهٔ بازو به کار می‌رود؛ نمایش نهایی آن به پشتیبانی نسخهٔ Bale و شرایط کاربر بستگی دارد.

~~~php
Bale::askReview(
    userId: 123456789,
    delaySeconds: 30,
);
~~~

هر دو مقدار عدد صحیح و الزامی‌اند. `delaySeconds` تأخیر نمایش فرم از زمان فراخوانی را برحسب ثانیه تعیین می‌کند و پاسخ موفق Bale برابر `true` است.

## تست ربات بله در Laravel بدون درخواست واقعی

برای تست اتصال لازم نیست URLهای Bale، ساختار پاسخ یا جزئیات `Http::fake()` را بدانید:

~~~php
Bale::fake();

$order->confirm();

Bale::assertSent('sendMessage', [
    'chat_id' => 123456789,
    'text' => 'سفارش شما تأیید شد',
]);
~~~

`Bale::fake()` فقط درخواست‌های Bot API و دانلود فایلِ همین پکیج را شبیه‌سازی می‌کند؛ درخواست HTTP نامرتبط برنامه را شبیه‌سازی یا جزو بررسی‌ها حساب نمی‌کند. API تست عبارت است از `Bale::fake()`، `Bale::assertSent()`، `Bale::assertSentTimes()`، `Bale::assertNotSent()` و `Bale::assertNothingSent()`. بررسی‌ها متد رسمی مانند `sendMessage` را می‌بینند، نه متدهای کمکی مانند `replyToMessage`. اگر در همان تست از `Http::fake()` فراگیر استفاده می‌کنید، ابتدا `Bale::fake()` را ثبت کنید؛ جزئیات، مثال پیشرفته‌تر و تست رسانه در [راهنمای تست](docs/testing.md) آمده است.

## چطور به یک پیام بله پاسخ بدهیم؟

روش رسمیِ سطح پایین، `sendMessage` با `reply_to_message_id` است:

~~~php
Bale::sendMessage(
    chatId: $message['chat']['id'],
    text: 'پاسخ شما',
    options: [
        'reply_to_message_id' => $message['message_id'],
    ],
);
~~~

برای این روند تکراری از متد کمکی پکیج استفاده کنید:

~~~php
Bale::replyToMessage(
    message: $message,
    text: 'پاسخ شما',
    options: [
        'reply_markup' => ['inline_keyboard' => []],
    ],
);
~~~

این متد فقط `message_id` و `chat.id` صحیح را از آرایهٔ خام Bale Message می‌خواند؛ هر دو باید عدد صحیح باشند. سپس `sendMessage` را فراخوانی می‌کند. `chat_id`، `text` و `reply_to_message_id` از خود روند می‌آیند و با `options` قابل جایگزینی نیستند. شکل کامل Message اعتبارسنجی یا به DTO تبدیل نمی‌شود؛ ورودی ناقص پیش از هر درخواست HTTP با `InvalidArgumentException` رد می‌شود.

## چطور دکمه‌های درون‌خطی و Callback Query بسازیم؟

آرایهٔ مستند Bale را مستقیماً به `reply_markup` بدهید؛ سازندهٔ دکمه لازم نیست:

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

هنگام دریافت `callback_query`، ابتدا Query را پاسخ دهید. فیلد `message` در CallbackQuery ممکن است وجود نداشته باشد؛ فقط در صورت وجود، پیام را ویرایش کنید:

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

این دو کار مستقل‌اند؛ پکیج متد کمکی ترکیبی Callback و ویرایش ندارد.

## ارسال فایل و رسانه: `file_id`، URL و آپلود محلی

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendAnimation` و `sendVoice`:

- رشتهٔ `string` بدون تغییر به Bale می‌رود و باید یک `file_id` بله یا HTTP URL باشد.
- `SplFileInfo` یعنی آپلود صریح فایل محلی با `multipart/form-data`.
- رشتهٔ مسیر فایل محلی به‌صورت خودکار آپلود نمی‌شود.

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

برای آلبوم از `sendMediaGroup` و آرایهٔ مستند Bale استفاده کنید. فایل پیوست محلی باید نامی روشن و `attach://name` متناظر داشته باشد.

## چطور فایل بله را دانلود کنیم؟

`getFile($fileId)` یک درخواست رسمی برای اطلاعات فایل است. `downloadFile($fileId)` متد کمکی پکیج است که ابتدا `getFile` را اجرا می‌کند، `file_path` غیرخالی را می‌گیرد و محتوای دودویی را برمی‌گرداند:

~~~php
$contents = Bale::downloadFile($fileId);
~~~

در برنامهٔ Laravel خودتان می‌توانید آن را با Storage ذخیره کنید؛ پکیج به Filesystem وابسته نیست:

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

طبق مستندات رسمی فعلی Bale، بازوها تا ۲۰ مگابایت فایل دانلود می‌کنند و لینک دانلود حاصل از `getFile` برای یک ساعت تضمین‌شده است؛ با فراخوانی دوبارهٔ `getFile` لینک تازه می‌شود. پکیج این محدودیت را محلی اعمال نمی‌کند، URL توکن‌دار را عمومی نمی‌کند و فایل را تبدیل یا MIME آن را بررسی نمی‌کند. اگر `file_path` معتبر نباشد، `UnexpectedValueException` پیش از درخواست دانلود رخ می‌دهد.

## دریافت Update به‌صورت دوره‌ای

`getUpdates` دقیقاً یک درخواست می‌سازد؛ حلقه، صف و نگه‌داری `offset` بر عهدهٔ برنامهٔ شماست:

~~~php
$updates = Bale::getUpdates([
    'offset' => $nextOffset,
    'limit' => 100,
    'timeout' => 30,
]);

// پس از پردازش، offset بعدی را در محل ذخیره‌سازی برنامهٔ خودتان نگه دارید.
~~~

برای `timeout` با عدد صحیح، پکیج پنج ثانیه مهلت اضافی انتقال HTTP در نظر می‌گیرد (حداقل ۳۰ ثانیه)؛ بدون تغییر دادهٔ ارسالی Bale.

## راه‌اندازی وب‌هوک در Laravel

یک راه کامل در Laravel 13 این است که مسیر را در `routes/web.php` نگه دارید و فقط همان نشانی را از CSRF خارج کنید. مسیر زیر دقیقاً این URL عمومی را می‌سازد: `https://example.test/bale/webhook`.

~~~php
// routes/web.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/bale/webhook', function (Request $request) {
    $update = $request->all();

    // منطق برنامه
});
~~~

چون `routes/web.php` در گروه میان‌افزار `web` قرار دارد، POST خارجی Bale توکن CSRF ندارد. در `bootstrap/app.php`، تابع موجود `withMiddleware` را این‌گونه کامل کنید تا فقط همان مسیر مستثنا شود:

~~~php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->preventRequestForgery(except: [
        'bale/webhook',
    ]);
})
~~~

حالا URL ثبت‌شده در Bale دقیقاً با مسیر یکی است:

~~~php
Bale::setWebhook('https://example.test/bale/webhook');
~~~

برای مشاهده یا حذف تنظیمات خروجی از `getWebhookInfo()` و `deleteWebhook()` استفاده کنید. پکیج مسیردهی وب‌هوک، کنترل‌کننده، میان‌افزار یا اعتبارسنجی راز فراهم نمی‌کند.

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
- HTTP ناموفق خارج از ساختار پاسخ معتبر Bale، از جمله دانلود ناموفق محتوای دودویی، رفتار معمول Laravel HTTP Client یعنی `Illuminate\Http\Client\RequestException` را حفظ می‌کند.
- پاسخ موفقِ بدساخت یا نتیجهٔ با شکل نامعتبر، `UnexpectedValueException` می‌دهد.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

## تفاوت Bale و Telegram Bot API

Bale از نظر نام‌گذاری به Telegram Bot API شباهت دارد، اما این پکیج فقط مستندات Bale را دنبال می‌کند. متد، گزینه یا رفتار ویژهٔ Telegram را فرض نکنید. برای هر کار از API پشتیبانی‌شدهٔ پکیج و مستندات رسمی Bale استفاده کنید.

## Laravel Boost و ابزارهای هوش مصنوعی

پکیج یک راهنمای Boost و مهارتی با نام `bale-development` دارد. در برنامهٔ استفاده‌کننده، بعد از نصب Laravel Boost این دستور را اجرا کنید:

~~~shell
php artisan boost:install
~~~

این مهارت به ابزارهای هوش مصنوعی کمک می‌کند API واقعی پکیج، تفاوت متد رسمی و متد کمکی، رسانه، دانلود فایل، Callback و مرز وب‌هوک و دریافت دوره‌ای را درست به کار ببرند.

## چه چیزهایی عمداً در این پکیج نیست؟

- چارچوب وب‌هوک، پردازشگر دریافت دوره‌ای یا فرمان Artisan برای دریافت دوره‌ای
- سامانهٔ فرمان و پردازش، وضعیت گفتگو یا DTOهای Message / Update / Chat
- سازندهٔ دکمه و متد کمکی ترکیبی Callback و ویرایش
- تشخیص خودکار مسیر فایل محلی
- URL عمومی فایلِ حاوی توکن بازو
- انتزاع `Storage` یا متد کمکی اختصاصی برای ذخیرهٔ فایل

این مرزها عمدی‌اند: Laravel Bale پکیجی برای API باقی می‌ماند، نه یک چارچوب.

## پرسش‌های متداول

### آیا این پکیج برای Laravel 12 کار می‌کند؟

خیر. محدودهٔ پشتیبانی پکیج Laravel 13 است.

### آیا رشتهٔ مسیر فایل به‌صورت خودکار آپلود می‌شود؟

خیر. `string` فقط `file_id` یا HTTP URL است؛ برای آپلود محلی از `SplFileInfo` استفاده کنید.

### آیا پکیج Webhook را خودش می‌سازد؟

خیر. `setWebhook` فقط URL خروجی Bale را پیکربندی می‌کند. مسیر دریافت‌کننده را در برنامهٔ Laravel خودتان می‌نویسید.

### آیا `getUpdates` خودش حلقه اجرا می‌کند؟

خیر؛ یک درخواست می‌فرستد. اجرای حلقه و نگه‌داری `offset` بر عهدهٔ برنامهٔ شماست.

### چطور فایل دریافتی را ذخیره کنم؟

در برنامهٔ Laravel خودتان، `Storage::put('path', Bale::downloadFile($fileId))` را استفاده کنید. پکیج به Filesystem وابسته نیست.

### آیا API بله دقیقاً همان Telegram Bot API است؟

خیر. شباهت نام‌ها مجوز فرض‌کردن متد API یا گزینه نیست؛ مستندات Bale مرجع است.

## توسعهٔ پکیج

~~~shell
composer install
composer test
~~~

تست‌ها با شبیه‌سازی HTTP در Laravel اجرا می‌شوند و نباید درخواست واقعی Bale یا توکن واقعی بسازند.
