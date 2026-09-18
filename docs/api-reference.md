---
title: مرجع API عمومی Laravel Bale
description: امضا، خروجی و مرز متدهای رسمی، متدهای کمکی و API تست Laravel Bale.
---

# مرجع API

این فهرست از متدهای عمومی فعلی `BaleClient` ساخته شده است. متدهای رسمی مستقیماً یک متد مستند Bale Bot API را فراخوانی می‌کنند؛ متدهای کمکی روندهای واقعی را ساده می‌کنند و APIهای تست، متد API نیستند.

## متدهای رسمی Bale API

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `getMe` | `()` | `array` | اطلاعات بازو |
| `sendMessage` | `(int|string $chatId, string $text, array $options = [])` | `array` | ارسال پیام متنی |
| `setWebhook` | `(string $url)` | `bool` | ثبت URL وب‌هوک |
| `deleteWebhook` | `()` | `bool` | حذف وب‌هوک |
| `getWebhookInfo` | `()` | `array` | اطلاعات وب‌هوک |
| `forwardMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | فوروارد پیام |
| `copyMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | کپی پیام |
| `sendChatAction` | `(int|string $chatId, string $action)` | `bool` | نمایش وضعیت گفتگو |
| `answerCallbackQuery` | `(string $callbackQueryId, array $options = [])` | `bool` | پاسخ به Callback Query |
| `askReview` | `(int $userId, int $delaySeconds)` | `bool` | درخواست ثبت یا ویرایش نظر؛ هر دو پارامتر عدد صحیح و الزامی‌اند |
| `editMessageText` | `(int|string $chatId, int $messageId, string $text, array $options = [])` | `mixed` | ویرایش متن؛ Bale نتیجهٔ نوع‌داری مستند نکرده است |
| `editMessageCaption` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش زیرنویس؛ نتیجهٔ خام Bale |
| `editMessageReplyMarkup` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش دکمه‌ها؛ نتیجهٔ خام Bale |
| `deleteMessage` | `(int|string $chatId, int $messageId)` | `bool` | حذف پیام |
| `getUpdates` | `(array $options = [])` | `array` | یک درخواست دریافت Update؛ حلقه نمی‌سازد |
| `sendPhoto` | `(int|string $chatId, int|string $fromChatId, string|SplFileInfo $photo, array $options = [])` | `array` | ارسال عکس؛ `fromChatId` طبق Bale الزامی است |
| `sendAudio` | `(int|string $chatId, string|SplFileInfo $audio, array $options = [])` | `array` | ارسال صدا |
| `sendDocument` | `(int|string $chatId, string|SplFileInfo $document, array $options = [])` | `array` | ارسال سند |
| `sendVideo` | `(int|string $chatId, string|SplFileInfo $video, array $options = [])` | `array` | ارسال ویدیو |
| `sendAnimation` | `(int|string $chatId, string|SplFileInfo $animation, array $options = [])` | `array` | ارسال انیمیشن |
| `sendVoice` | `(int|string $chatId, string|SplFileInfo $voice, array $options = [])` | `array` | ارسال پیام صوتی |
| `sendMediaGroup` | `(int|string $chatId, array $media, array $options = [], array $attachments = [])` | `array` | ارسال آلبوم؛ فایل‌های پیوست محلی باید با `attach://` هماهنگ باشند |
| `sendLocation` | `(int|string $chatId, float $latitude, float $longitude, array $options = [])` | `array` | ارسال موقعیت |
| `sendContact` | `(int|string $chatId, int|string $phoneNumber, string $firstName, array $options = [])` | `array` | ارسال مخاطب |
| `getFile` | `(string $fileId)` | `array` | اطلاعات فایل |

در متدهایی که `$options` دارند، مقدارهای الزامی پکیج بر کلید همنام در `options` مقدم‌اند. رشتهٔ رسانه فقط `file_id` یا HTTP URL است؛ برای آپلود محلی از `SplFileInfo` استفاده کنید.

## متدهای کمکی Laravel Bale

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `replyToMessage` | `(array $message, string $text, array $options = [])` | `array` | اعتبارسنجی دادهٔ خام Message و فراخوانی `sendMessage` |
| `downloadFile` | `(string $fileId)` | `string` | فراخوانی `getFile` و دانلود محتوای دودویی؛ `file_path` لازم است |

این‌ها متد رسمی Bale نیستند و در بررسی‌های تست با نام متد رسمی‌شان دیده می‌شوند.

## API تست Laravel Bale

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `fake` | `()` | `void` | شبیه‌سازی درخواست‌های پکیج Bale و پاک‌کردن سابقهٔ شبیه‌سازی |
| `assertSent` | `(string $method, array|Closure|null $condition = null)` | `void` | وجود متد API؛ آرایه باید با دادهٔ ارسالی منطبق باشد |
| `assertSentTimes` | `(string $method, int $times)` | `void` | تعداد فراخوانی متد رسمی |
| `assertNotSent` | `(string $method, array|Closure|null $condition = null)` | `void` | نبودن متد API یا شرط |
| `assertNothingSent` | `()` | `void` | نبودن هر درخواست Bot API یا دانلود فایل این پکیج |

برای تابع بررسی، آرگومان `Closure` یک `Illuminate\Http\Client\Request` دریافت می‌کند. جزئیات در [راهنمای تست](testing.md) است.
