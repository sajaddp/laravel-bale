---
title: مرجع رابط برنامه‌نویسی عمومی لاراول بله
description: امضا، خروجی و مرز متدهای رسمی، متدهای کمکی و ابزار آزمون لاراول بله.
---

# مرجع رابط برنامه‌نویسی

این فهرست از متدهای عمومی فعلی `BaleClient` ساخته شده است. متدهای رسمی مستقیماً یک متد مستند رابط برنامه‌نویسی بازوی بله را فراخوانی می‌کنند؛ متدهای کمکی روندهای واقعی را ساده می‌کنند و ابزارهای آزمون، متدهای رابط برنامه‌نویسی نیستند.

## متدهای رسمی بله

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `getMe` | `()` | `array` | اطلاعات بازو |
| `sendMessage` | `(int|string $chatId, string $text, array $options = [])` | `array` | ارسال پیام متنی |
| `setWebhook` | `(string $url)` | `bool` | ثبت نشانی وب‌هوک |
| `deleteWebhook` | `()` | `bool` | حذف وب‌هوک |
| `getWebhookInfo` | `()` | `array` | اطلاعات وب‌هوک |
| `forwardMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | فوروارد پیام |
| `copyMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | کپی پیام |
| `sendChatAction` | `(int|string $chatId, string $action)` | `bool` | نمایش وضعیت گفتگو |
| `answerCallbackQuery` | `(string $callbackQueryId, array $options = [])` | `bool` | پاسخ به انتخاب کاربر |
| `askReview` | `(int $userId, int $delaySeconds)` | `bool` | درخواست ثبت یا ویرایش نظر؛ هر دو پارامتر عدد صحیح و الزامی‌اند |
| `editMessageText` | `(int|string $chatId, int $messageId, string $text, array $options = [])` | `mixed` | ویرایش متن؛ بله نتیجهٔ نوع‌داری مستند نکرده است |
| `editMessageCaption` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش زیرنویس؛ نتیجهٔ خام بله |
| `editMessageReplyMarkup` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش دکمه‌ها؛ نتیجهٔ خام بله |
| `deleteMessage` | `(int|string $chatId, int $messageId)` | `bool` | حذف پیام |
| `getUpdates` | `(array $options = [])` | `array` | یک درخواست دریافت رویداد؛ حلقه نمی‌سازد |
| `sendPhoto` | `(int|string $chatId, int|string $fromChatId, string|SplFileInfo $photo, array $options = [])` | `array` | ارسال عکس؛ `fromChatId` طبق مستندات بله الزامی است |
| `sendAudio` | `(int|string $chatId, string|SplFileInfo $audio, array $options = [])` | `array` | ارسال صدا |
| `sendDocument` | `(int|string $chatId, string|SplFileInfo $document, array $options = [])` | `array` | ارسال سند |
| `sendVideo` | `(int|string $chatId, string|SplFileInfo $video, array $options = [])` | `array` | ارسال ویدیو |
| `sendAnimation` | `(int|string $chatId, string|SplFileInfo $animation, array $options = [])` | `array` | ارسال انیمیشن |
| `sendVoice` | `(int|string $chatId, string|SplFileInfo $voice, array $options = [])` | `array` | ارسال پیام صوتی |
| `sendMediaGroup` | `(int|string $chatId, array $media, array $options = [], array $attachments = [])` | `array` | ارسال آلبوم؛ فایل‌های پیوست محلی باید با `attach://` هماهنگ باشند |
| `sendLocation` | `(int|string $chatId, float $latitude, float $longitude, array $options = [])` | `array` | ارسال موقعیت |
| `sendContact` | `(int|string $chatId, int|string $phoneNumber, string $firstName, array $options = [])` | `array` | ارسال مخاطب |
| `getFile` | `(string $fileId)` | `array` | اطلاعات فایل |

در متدهایی که `$options` دارند، مقدارهای الزامی پکیج بر کلید همنام در `options` مقدم‌اند. رشتهٔ رسانه فقط `file_id` یا نشانی اچ‌تی‌تی‌پی است؛ برای بارگذاری محلی از `SplFileInfo` استفاده کنید.

## متدهای کمکی لاراول بله

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `replyToMessage` | `(array $message, string $text, array $options = [])` | `array` | اعتبارسنجی دادهٔ خام پیام و فراخوانی `sendMessage` |
| `downloadFile` | `(string $fileId)` | `string` | فراخوانی `getFile` و دانلود محتوای دودویی؛ `file_path` لازم است |

این‌ها متد رسمی بله نیستند و در بررسی‌های آزمون با نام متد رسمی‌شان دیده می‌شوند.

## ابزار آزمون لاراول بله

| متد | امضا | خروجی | کاربرد |
| --- | --- | --- | --- |
| `fake` | `()` | `void` | شبیه‌سازی درخواست‌های پکیج بله و پاک‌کردن سابقهٔ شبیه‌سازی |
| `assertSent` | `(string $method, array|Closure|null $condition = null)` | `void` | وجود فراخوانی متد؛ آرایه باید با دادهٔ ارسالی منطبق باشد |
| `assertSentTimes` | `(string $method, int $times)` | `void` | تعداد فراخوانی متد رسمی |
| `assertNotSent` | `(string $method, array|Closure|null $condition = null)` | `void` | نبودن متد یا شرط |
| `assertNothingSent` | `()` | `void` | نبودن هر درخواست رابط برنامه‌نویسی بازو یا دانلود فایل این پکیج |

برای تابع بررسی، آرگومان `Closure` یک `Illuminate\Http\Client\Request` دریافت می‌کند. جزئیات در [راهنمای آزمون](testing.md) است.
