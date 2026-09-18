---
title: Callback Query و دکمه‌های درون‌خطی بله در Laravel
description: راهنمای ساختار خام InlineKeyboardMarkup، callback_data، url و پاسخ به Callback Query با Laravel Bale.
---

# چطور به Callback Query بله پاسخ بدهیم؟

برای دکمه‌های درون‌خطی، آرایهٔ خام مستند Bale را مستقیماً در `reply_markup` بفرستید. Laravel Bale سازندهٔ دکمه یا مسیردهی Callback ندارد.

~~~php
use Sajaddp\Bale\Facades\Bale;

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

`callback_data` دادهٔ انتخاب برنامهٔ شماست و `url` کاربر را به لینک می‌برد. گزینه‌ها را فقط مطابق مستندات Bale برای همان ساختار بفرستید.

## ابتدا Query را پاسخ دهید

پس از دریافت `callback_query`، `answerCallbackQuery` را فراخوانی کنید تا حالت انتظار دکمه پایان یابد. سپس فقط در صورت وجود پیام همراه Query، آن را ویرایش کنید؛ `CallbackQuery.message` اختیاری است.

~~~php
$callback = $update['callback_query'];

Bale::answerCallbackQuery(
    callbackQueryId: $callback['id'],
    options: ['text' => 'ثبت شد'],
);

if (isset($callback['message'])) {
    Bale::editMessageText(
        chatId: $callback['message']['chat']['id'],
        messageId: $callback['message']['message_id'],
        text: 'ثبت شد',
    );
}
~~~

پاسخ به Query و ویرایش پیام دو کار مستقل‌اند. برای APIهای `editMessageCaption`، `editMessageReplyMarkup` و حذف پیام، [راهنمای پیام‌ها](messages.md) را بخوانید و برای امضاها به [مرجع API](api-reference.md) رجوع کنید.
