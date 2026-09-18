# لاراول بله — پکیج لاراول ۱۳ برای رابط برنامه‌نویسی بازوی بله

[![آزمون‌ها](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml/badge.svg)](https://github.com/sajaddp/laravel-bale/actions/workflows/tests.yml)

پکیجی کم‌حجم برای کار با رابط برنامه‌نویسی بازوی بله در لاراول ۱۳.

`sajaddp/laravel-bale` پکیجی کم‌حجم برای کار با رابط برنامه‌نویسی بازوی بله در لاراول ۱۳ است. این پکیج پی‌اچ‌پی ۸٫۳ یا جدیدتر را پشتیبانی می‌کند، پاسخ‌های مستند بله را به آرایه یا مقدار نتیجه تبدیل می‌کند و عمداً چارچوب ساخت ربات نیست.

## چرا لاراول بله؟

برای کارهای رایج مانند ارسال پیام، رسانه، وب‌هوک، دریافت دوره‌ای، پاسخ انتخاب و فایل، از کلاس دسترسی و کارخواه اچ‌تی‌تی‌پی لاراول استفاده می‌کنید؛ بدون مسیر، پردازشگر یا وضعیت پنهان. رفتار بله همیشه از مستندات رسمی آن می‌آید، نه از فرض سازگاری با تلگرام.

## نیازمندی‌ها

- لاراول ۱۳
- پی‌اچ‌پی ۸٫۳ یا جدیدتر

## نصب و تنظیم `BALE_BOT_TOKEN`

این پکیج در زمان نگارش هنوز در پکیجیست منتشر نشده است؛ پس فرمان زیر دستور نصب موردنظر پس از انتشار است، نه یک نصب عمومیِ فعال در حال حاضر:

~~~shell
composer require sajaddp/laravel-bale
~~~

پس از نصب، لاراول ارائه‌دهندهٔ پکیج را خودکار پیدا می‌کند. توکن بازو را در محیط برنامه قرار دهید:

~~~dotenv
BALE_BOT_TOKEN=your-bot-token
~~~

در صورت نیاز، تنها تنظیم واقعی پکیج را منتشر کنید:

~~~shell
php artisan vendor:publish --tag=bale-config
~~~

کلاس دسترسی را صریحاً وارد کنید:

~~~php
use Sajaddp\Bale\Facades\Bale;
~~~

## راهنمای عمیق‌تر

این راهنما برای شروع سریع است. مستندات ساخت‌یافتهٔ مخزن جزئیات و مرجع کامل را نگه می‌دارند:

- [مستندات آنلاین](https://sajaddehshiri.ir/laravel-bale/)
- [شروع کار](docs/getting-started.md)
- [پیام‌ها](docs/messages.md)
- [وب‌هوک](docs/webhooks.md)
- [دریافت دوره‌ای](docs/polling.md)
- [فایل و رسانه](docs/files-media.md)
- [دکمه‌ها و پاسخ انتخاب](docs/callbacks-keyboards.md)
- [مرجع رابط برنامه‌نویسی](docs/api-reference.md)
- [پوشش رابط برنامه‌نویسی بازوی بله](docs/api-coverage.md)
- [آزمون](docs/testing.md)
- [دستورپخت‌ها](docs/recipes.md)
- [رفع اشکال](docs/troubleshooting.md)
- [راهنمای هوش مصنوعی](docs/ai-coding.md)

## شروع سریع: چطور با لاراول به بله پیام بفرستیم؟

~~~php
use Sajaddp\Bale\Facades\Bale;

$bot = Bale::getMe();

$message = Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام',
);
~~~

آرگومان‌های الزامی هر متد بر کلید همنام در `options` مقدم‌اند. برای `options` فقط فیلدهایی را بفرستید که بله برای همان متد مستند کرده است.

## متدهای رسمی پشتیبانی‌شدهٔ بله

این جدول از متدهای عمومی فعلی `BaleClient` ساخته شده است. همهٔ موارد این بخش متدهای مستقیمِ رسمی بله هستند، نه متدی خیالی.

| گروه | متد پکیج | کاربرد | خروجی |
| --- | --- | --- | --- |
| بازو | `getMe` | اطلاعات بازو | `array` |
| پیام‌ها | `sendMessage` | ارسال متن | `array` |
| پیام‌ها | `forwardMessage` / `copyMessage` | فوروارد یا کپی پیام | `array` |
| پیام‌ها | `sendChatAction` | نمایش وضعیت در گفتگو | `bool` |
| وب‌هوک | `setWebhook` / `deleteWebhook` | ثبت یا حذف وب‌هوک خروجی | `bool` |
| وب‌هوک | `getWebhookInfo` | اطلاعات وب‌هوک | `array` |
| رویدادها | `getUpdates` | دریافت یک‌بارهٔ رویدادها | `array` |
| پاسخ انتخاب | `answerCallbackQuery` | پاسخ به انتخاب کاربر | `bool` |
| نظر | `askReview` | درخواست ثبت یا ویرایش نظر دربارهٔ بازو | `bool` |
| کار با پیام | `editMessageText` / `editMessageCaption` / `editMessageReplyMarkup` | ویرایش پیام یا دکمه‌های درون‌خطی | نتیجهٔ خام بله |
| کار با پیام | `deleteMessage` | حذف پیام | `bool` |
| رسانه و فایل | `sendPhoto` / `sendAudio` / `sendDocument` / `sendVideo` / `sendAnimation` / `sendVoice` | ارسال یک رسانه | `array` |
| رسانه و فایل | `sendMediaGroup` | ارسال گروه رسانه | `array` |
| رسانه و فایل | `getFile` | دریافت اطلاعات فایل | `array` |
| موقعیت و مخاطب | `sendLocation` / `sendContact` | ارسال موقعیت یا مخاطب | `array` |

## امکانات اضافهٔ لاراول بله

دو متد زیر مستقیماً متد بله نیستند. آن‌ها فقط روندهای تکراری را با متدهای رسمی بالا ساده می‌کنند؛ این تمایز برای ابزارهای هوش مصنوعی هم مهم است.

| متد کمکی لاراول بله | بر پایهٔ متد رسمی | خروجی |
| --- | --- | --- |
| `replyToMessage` | `sendMessage` و `reply_to_message_id` | `array` |
| `downloadFile` | `getFile` و دانلود فایل مستندشدهٔ بله | محتوای دودویی `string` |

## درخواست نظر با `askReview`

`askReview` متد رسمی بله است، نه متد کمکی پکیج. طبق مستندات رسمی، برای نمایش فرم ثبت یا ویرایش نظر دربارهٔ بازو به کار می‌رود؛ نمایش نهایی آن به پشتیبانی نسخهٔ بله و شرایط کاربر بستگی دارد.

~~~php
Bale::askReview(
    userId: 123456789,
    delaySeconds: 30,
);
~~~

هر دو مقدار عدد صحیح و الزامی‌اند. `delaySeconds` تأخیر نمایش فرم از زمان فراخوانی را برحسب ثانیه تعیین می‌کند و پاسخ موفق بله برابر `true` است.

## آزمون ربات بله در لاراول بدون درخواست واقعی

برای آزمون اتصال لازم نیست نشانی‌های بله، ساختار پاسخ یا جزئیات `Http::fake()` را بدانید:

~~~php
Bale::fake();

$order->confirm();

Bale::assertSent('sendMessage', [
    'chat_id' => 123456789,
    'text' => 'سفارش شما تأیید شد',
]);
~~~

`Bale::fake()` فقط درخواست‌های رابط برنامه‌نویسی بازو و دانلود فایلِ همین پکیج را شبیه‌سازی می‌کند؛ درخواست نامرتبط برنامه را شبیه‌سازی یا جزو بررسی‌ها حساب نمی‌کند. ابزار آزمون شامل `Bale::fake()`، `Bale::assertSent()`، `Bale::assertSentTimes()`، `Bale::assertNotSent()` و `Bale::assertNothingSent()` است. بررسی‌ها متد رسمی مانند `sendMessage` را می‌بینند، نه متدهای کمکی مانند `replyToMessage`. اگر در همان آزمون از `Http::fake()` فراگیر استفاده می‌کنید، ابتدا `Bale::fake()` را ثبت کنید؛ جزئیات، مثال پیشرفته‌تر و آزمون رسانه در [راهنمای آزمون](docs/testing.md) آمده است.

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

این متد فقط `message_id` و `chat.id` صحیح را از آرایهٔ خام پیام بله می‌خواند؛ هر دو باید عدد صحیح باشند. سپس `sendMessage` را فراخوانی می‌کند. `chat_id`، `text` و `reply_to_message_id` از خود روند می‌آیند و با `options` قابل جایگزینی نیستند. شکل کامل پیام اعتبارسنجی یا به شیء انتقال داده تبدیل نمی‌شود؛ ورودی ناقص پیش از هر درخواست اچ‌تی‌تی‌پی با `InvalidArgumentException` رد می‌شود.

## چطور دکمه‌های درون‌خطی و پاسخ انتخاب بسازیم؟

آرایهٔ مستند بله را مستقیماً به `reply_markup` بدهید؛ سازندهٔ دکمه لازم نیست:

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

هنگام دریافت `callback_query`، ابتدا انتخاب را پاسخ دهید. فیلد `message` در دادهٔ انتخاب ممکن است وجود نداشته باشد؛ فقط در صورت وجود، پیام را ویرایش کنید:

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

این دو کار مستقل‌اند؛ پکیج متد کمکی ترکیبی پاسخ انتخاب و ویرایش ندارد.

## ارسال فایل و رسانه: `file_id`، نشانی و بارگذاری محلی

برای `sendPhoto`، `sendAudio`، `sendDocument`، `sendVideo`، `sendAnimation` و `sendVoice`:

- رشتهٔ `string` بدون تغییر به بله می‌رود و باید یک `file_id` بله یا نشانی اچ‌تی‌تی‌پی باشد.
- `SplFileInfo` یعنی بارگذاری صریح فایل محلی با دادهٔ چندبخشی.
- رشتهٔ مسیر فایل محلی به‌صورت خودکار بارگذاری نمی‌شود.

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

مستندات فعلی بله برای `sendPhoto`، پارامتر `from_chat_id` را نیز الزامی می‌داند:

~~~php
Bale::sendPhoto(
    chatId: '@target_channel',
    fromChatId: '@source_channel',
    photo: new SplFileInfo(storage_path('app/example.jpg')),
);
~~~

برای آلبوم از `sendMediaGroup` و آرایهٔ مستند بله استفاده کنید. فایل پیوست محلی باید نامی روشن و `attach://name` متناظر داشته باشد.

## چطور فایل بله را دانلود کنیم؟

`getFile($fileId)` یک درخواست رسمی برای اطلاعات فایل است. `downloadFile($fileId)` متد کمکی پکیج است که ابتدا `getFile` را اجرا می‌کند، `file_path` غیرخالی را می‌گیرد و محتوای دودویی را برمی‌گرداند:

~~~php
$contents = Bale::downloadFile($fileId);
~~~

در برنامهٔ لاراول خودتان می‌توانید آن را با `Storage` ذخیره کنید؛ پکیج به سامانهٔ فایل وابسته نیست:

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put(
    'bale/document.pdf',
    Bale::downloadFile($fileId),
);
~~~

طبق مستندات رسمی فعلی بله، بازوها تا ۲۰ مگابایت فایل دانلود می‌کنند و پیوند دانلود حاصل از `getFile` برای یک ساعت تضمین‌شده است؛ با فراخوانی دوبارهٔ `getFile` پیوند تازه می‌شود. پکیج این محدودیت را محلی اعمال نمی‌کند، نشانی توکن‌دار را عمومی نمی‌کند و فایل را تبدیل یا گونهٔ محتوای آن را بررسی نمی‌کند. اگر `file_path` معتبر نباشد، `UnexpectedValueException` پیش از درخواست دانلود رخ می‌دهد.

## دریافت رویداد به‌صورت دوره‌ای

`getUpdates` دقیقاً یک درخواست می‌سازد؛ حلقه، صف و نگه‌داری `offset` بر عهدهٔ برنامهٔ شماست:

~~~php
$updates = Bale::getUpdates([
    'offset' => $nextOffset,
    'limit' => 100,
    'timeout' => 30,
]);

// پس از پردازش، offset بعدی را در محل ذخیره‌سازی برنامهٔ خودتان نگه دارید.
~~~

برای `timeout` با عدد صحیح، پکیج پنج ثانیه مهلت اضافی انتقال اچ‌تی‌تی‌پی در نظر می‌گیرد (حداقل ۳۰ ثانیه)؛ بدون تغییر دادهٔ ارسالی بله.

## راه‌اندازی وب‌هوک در لاراول

یک راه کامل در لاراول ۱۳ این است که مسیر را در `routes/web.php` نگه دارید و فقط همان نشانی را از محافظت درخواست خارج کنید. مسیر زیر دقیقاً نشانی عمومی `https://example.test/bale/webhook` را می‌سازد.

~~~php
// routes/web.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/bale/webhook', function (Request $request) {
    $update = $request->all();

    // منطق برنامه
});
~~~

چون `routes/web.php` در گروه میان‌افزار `web` قرار دارد، درخواست خارجی بله توکن محافظت درخواست ندارد. در `bootstrap/app.php`، تابع موجود `withMiddleware` را این‌گونه کامل کنید تا فقط همان مسیر مستثنا شود:

~~~php
use Illuminate\Foundation\Configuration\Middleware;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->preventRequestForgery(except: [
        'bale/webhook',
    ]);
})
~~~

حالا نشانی ثبت‌شده در بله دقیقاً با مسیر یکی است:

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

- پاسخ معتبر بله با `ok: false`، `Sajaddp\Bale\Exceptions\BaleRequestException` می‌دهد.
- پاسخ ناموفق اچ‌تی‌تی‌پی خارج از ساختار پاسخ معتبر بله، از جمله دانلود ناموفق محتوای دودویی، رفتار معمول کارخواه اچ‌تی‌تی‌پی لاراول یعنی `Illuminate\Http\Client\RequestException` را حفظ می‌کند.
- پاسخ موفقِ بدساخت یا نتیجهٔ با شکل نامعتبر، `UnexpectedValueException` می‌دهد.

~~~php
use Sajaddp\Bale\Exceptions\BaleRequestException;

try {
    Bale::sendMessage(chatId: 123456789, text: 'سلام');
} catch (BaleRequestException $exception) {
    report($exception->description);
}
~~~

## تفاوت بله و رابط برنامه‌نویسی بازوی تلگرام

بله از نظر نام‌گذاری به رابط برنامه‌نویسی بازوی تلگرام شباهت دارد، اما این پکیج فقط مستندات بله را دنبال می‌کند. متد، گزینه یا رفتار ویژهٔ تلگرام را فرض نکنید. برای هر کار از متد پشتیبانی‌شدهٔ پکیج و مستندات رسمی بله استفاده کنید.

## بوست لاراول و ابزارهای هوش مصنوعی

پکیج یک راهنمای بوست و مهارتی با نام `bale-development` دارد. در برنامهٔ استفاده‌کننده، بعد از نصب بوست لاراول این دستور را اجرا کنید:

~~~shell
php artisan boost:install
~~~

این مهارت به ابزارهای هوش مصنوعی کمک می‌کند متدهای واقعی پکیج، تفاوت متد رسمی و متد کمکی، رسانه، دانلود فایل، پاسخ انتخاب و مرز وب‌هوک و دریافت دوره‌ای را درست به کار ببرند.

## چه چیزهایی عمداً در این پکیج نیست؟

- چارچوب وب‌هوک، پردازشگر دریافت دوره‌ای یا فرمان آرتیزان برای دریافت دوره‌ای
- سامانهٔ فرمان و پردازش، وضعیت گفتگو یا شیءهای انتقال داده برای پیام، رویداد و گفتگو
- سازندهٔ دکمه و متد کمکی ترکیبی پاسخ انتخاب و ویرایش
- تشخیص خودکار مسیر فایل محلی
- نشانی عمومی فایلِ حاوی توکن بازو
- انتزاع `Storage` یا متد کمکی اختصاصی برای ذخیرهٔ فایل

این مرزها عمدی‌اند: لاراول بله پکیجی برای رابط برنامه‌نویسی باقی می‌ماند، نه یک چارچوب.

## پرسش‌های متداول

### آیا این پکیج برای لاراول ۱۲ کار می‌کند؟

خیر. محدودهٔ پشتیبانی پکیج لاراول ۱۳ است.

### آیا رشتهٔ مسیر فایل به‌صورت خودکار آپلود می‌شود؟

خیر. `string` فقط `file_id` یا نشانی اچ‌تی‌تی‌پی است؛ برای بارگذاری محلی از `SplFileInfo` استفاده کنید.

### آیا پکیج وب‌هوک را خودش می‌سازد؟

خیر. `setWebhook` فقط نشانی خروجی بله را پیکربندی می‌کند. مسیر دریافت‌کننده را در برنامهٔ لاراول خودتان می‌نویسید.

### آیا `getUpdates` خودش حلقه اجرا می‌کند؟

خیر؛ یک درخواست می‌فرستد. اجرای حلقه و نگه‌داری `offset` بر عهدهٔ برنامهٔ شماست.

### چطور فایل دریافتی را ذخیره کنم؟

در برنامهٔ لاراول خودتان، `Storage::put('path', Bale::downloadFile($fileId))` را استفاده کنید. پکیج به سامانهٔ فایل وابسته نیست.

### آیا رابط برنامه‌نویسی بله دقیقاً همان رابط برنامه‌نویسی بازوی تلگرام است؟

خیر. شباهت نام‌ها مجوز فرض‌کردن متد یا گزینه نیست؛ مستندات بله مرجع است.

## توسعهٔ پکیج

~~~shell
composer install
composer test
~~~

آزمون‌ها با شبیه‌سازی اچ‌تی‌تی‌پی در لاراول اجرا می‌شوند و نباید درخواست واقعی بله یا توکن واقعی بسازند.
