# مرجع API

این فهرست از public methodهای فعلی `BaleClient` ساخته شده است. wrapperهای رسمی مستقیماً یک method مستند Bale Bot API را فراخوانی می‌کنند؛ convenienceها workflowهای واقعی را compose می‌کنند و testing APIها endpoint نیستند.

## Official Bale API wrappers

| Method | Signature | Return | Purpose |
| --- | --- | --- | --- |
| `getMe` | `()` | `array` | اطلاعات بازو |
| `sendMessage` | `(int|string $chatId, string $text, array $options = [])` | `array` | ارسال پیام متنی |
| `setWebhook` | `(string $url)` | `bool` | ثبت URL وب‌هوک |
| `deleteWebhook` | `()` | `bool` | حذف وب‌هوک |
| `getWebhookInfo` | `()` | `array` | اطلاعات وب‌هوک |
| `forwardMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | فوروارد پیام |
| `copyMessage` | `(int|string $chatId, int|string $fromChatId, int $messageId)` | `array` | کپی پیام |
| `sendChatAction` | `(int|string $chatId, string $action)` | `bool` | نمایش action گفتگو |
| `answerCallbackQuery` | `(string $callbackQueryId, array $options = [])` | `bool` | پاسخ به Callback Query |
| `askReview` | `(int $userId, int $delaySeconds)` | `bool` | درخواست ثبت یا ویرایش نظر؛ هر دو پارامتر integer و الزامی‌اند |
| `editMessageText` | `(int|string $chatId, int $messageId, string $text, array $options = [])` | `mixed` | ویرایش متن؛ Bale نتیجهٔ نوع‌داری مستند نکرده است |
| `editMessageCaption` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش زیرنویس؛ نتیجهٔ خام Bale |
| `editMessageReplyMarkup` | `(int|string $chatId, int $messageId, array $options = [])` | `mixed` | ویرایش keyboard؛ نتیجهٔ خام Bale |
| `deleteMessage` | `(int|string $chatId, int $messageId)` | `bool` | حذف پیام |
| `getUpdates` | `(array $options = [])` | `array` | یک درخواست دریافت Update؛ loop نمی‌سازد |
| `sendPhoto` | `(int|string $chatId, int|string $fromChatId, string|SplFileInfo $photo, array $options = [])` | `array` | ارسال عکس؛ `fromChatId` طبق Bale الزامی است |
| `sendAudio` | `(int|string $chatId, string|SplFileInfo $audio, array $options = [])` | `array` | ارسال صدا |
| `sendDocument` | `(int|string $chatId, string|SplFileInfo $document, array $options = [])` | `array` | ارسال سند |
| `sendVideo` | `(int|string $chatId, string|SplFileInfo $video, array $options = [])` | `array` | ارسال ویدیو |
| `sendAnimation` | `(int|string $chatId, string|SplFileInfo $animation, array $options = [])` | `array` | ارسال انیمیشن |
| `sendVoice` | `(int|string $chatId, string|SplFileInfo $voice, array $options = [])` | `array` | ارسال voice |
| `sendMediaGroup` | `(int|string $chatId, array $media, array $options = [], array $attachments = [])` | `array` | ارسال album؛ attachmentهای محلی باید با `attach://` هماهنگ باشند |
| `sendLocation` | `(int|string $chatId, float $latitude, float $longitude, array $options = [])` | `array` | ارسال موقعیت |
| `sendContact` | `(int|string $chatId, int|string $phoneNumber, string $firstName, array $options = [])` | `array` | ارسال مخاطب |
| `getFile` | `(string $fileId)` | `array` | metadata فایل |

در methodهایی که `$options` دارند، مقادیر required روش پکیج بر کلید همنام در options مقدم‌اند. `string` رسانه فقط `file_id` یا HTTP URL است؛ برای آپلود محلی از `SplFileInfo` استفاده کنید.

## Laravel Bale convenience methods

| Method | Signature | Return | Purpose |
| --- | --- | --- | --- |
| `replyToMessage` | `(array $message, string $text, array $options = [])` | `array` | اعتبارسنجی raw Message و composition با `sendMessage` |
| `downloadFile` | `(string $fileId)` | `string` | composition با `getFile` و دانلود binary؛ `file_path` لازم است |

این‌ها method رسمی Bale نیستند و در assertionهای تست به نام endpoint رسمی‌شان دیده می‌شوند.

## Laravel Bale testing API

| Method | Signature | Return | Purpose |
| --- | --- | --- | --- |
| `fake` | `()` | `void` | fake کردن درخواست‌های package Bale و پاک‌کردن history fake |
| `assertSent` | `(string $method, array|Closure|null $condition = null)` | `void` | وجود endpoint؛ array یک subset strict است |
| `assertSentTimes` | `(string $method, int $times)` | `void` | تعداد endpoint رسمی |
| `assertNotSent` | `(string $method, array|Closure|null $condition = null)` | `void` | نبودن endpoint یا شرط |
| `assertNothingSent` | `()` | `void` | نبودن هر درخواست Bot API یا دانلود فایل این پکیج |

برای callback، آرگومان `Closure` یک `Illuminate\Http\Client\Request` دریافت می‌کند. جزئیات در [راهنمای تست](testing.md) است.
