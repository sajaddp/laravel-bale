---
title: دکمه‌های درون‌خطی و پاسخ انتخاب بله در لاراول
description: راهنمای ساختار خام دکمه‌ها، دادهٔ انتخاب، نشانی و پاسخ به انتخاب کاربر با لاراول بله.
---

# چطور به انتخاب کاربر در بله پاسخ بدهیم؟

برای دکمه‌های درون‌خطی، آرایهٔ خام مستند بله را مستقیماً در `reply_markup` بفرستید. لاراول بله سازندهٔ دکمه یا مسیردهی پاسخ انتخاب ندارد.

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

`callback_data` دادهٔ انتخاب برنامهٔ شماست و `url` کاربر را به نشانی پیوند می‌برد. گزینه‌ها را فقط مطابق مستندات بله برای همان ساختار بفرستید.

## ابتدا انتخاب را پاسخ دهید

پس از دریافت `callback_query`، `answerCallbackQuery` را فراخوانی کنید تا حالت انتظار دکمه پایان یابد. سپس فقط در صورت وجود پیام همراه انتخاب، آن را ویرایش کنید؛ `message` در دادهٔ انتخاب اختیاری است.

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

پاسخ به انتخاب و ویرایش پیام دو کار مستقل‌اند. برای متدهای `editMessageCaption`، `editMessageReplyMarkup` و حذف پیام، [راهنمای پیام‌ها](messages.md) را بخوانید و برای امضاها به [مرجع رابط برنامه‌نویسی](api-reference.md) رجوع کنید.
