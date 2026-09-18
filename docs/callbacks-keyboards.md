---
title: Callback Query و Inline Keyboard بله در Laravel
description: راهنمای InlineKeyboardMarkup خام، callback_data، url و پاسخ به Callback Query با Laravel Bale.
---

# چطور Callback Query بله را پاسخ بدهیم؟

برای Inline Keyboard، شکل raw array مستند Bale را مستقیماً در `reply_markup` بفرستید. Laravel Bale keyboard builder یا callback router ندارد.

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

`callback_data` دادهٔ انتخاب application شماست و `url` کاربر را به لینک می‌برد. گزینه‌ها را فقط مطابق مستندات Bale برای همان object بفرستید.

## ابتدا Query را پاسخ دهید

پس از دریافت `callback_query`، `answerCallbackQuery` را فراخوانی کنید تا وضعیت انتظار دکمه پایان یابد. سپس، تنها اگر message همراه Query وجود دارد، آن را ویرایش کنید؛ `CallbackQuery.message` اختیاری است.

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

پاسخ به Query و ویرایش پیام دو عملیات مستقل‌اند. برای APIهای `editMessageCaption`، `editMessageReplyMarkup` و حذف پیام، [راهنمای پیام‌ها](messages.md) را بخوانید و برای signatureها به [مرجع API](api-reference.md) رجوع کنید.
