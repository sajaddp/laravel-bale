---
title: ارسال و مدیریت پیام بله در Laravel
description: راهنمای ارسال، پاسخ، فوروارد، کپی، ویرایش، حذف و action پیام بله با Laravel Bale.
---

# چطور در Laravel به بله پیام بفرستیم؟

برای ارسال متن از wrapper رسمی `sendMessage` استفاده کنید. شناسهٔ گفتگو می‌تواند integer یا شناسهٔ string مستند Bale باشد.

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام از Laravel',
);
~~~

کلیدهای required method بر کلید همنام در `options` مقدم‌اند. فقط optionهایی را بفرستید که Bale برای همان endpoint مستند کرده است.

## پاسخ به پیام

روش رسمی، ارسال `reply_to_message_id` با `sendMessage` است:

~~~php
Bale::sendMessage(
    chatId: $message['chat']['id'],
    text: 'پاسخ شما',
    options: ['reply_to_message_id' => $message['message_id']],
);
~~~

`replyToMessage` convenience پکیج همین workflow تکراری را compose می‌کند. ورودی آن باید raw Bale Message با `message_id` integer و `chat.id` integer باشد؛ ورودی ناقص پیش از هر درخواست HTTP با `InvalidArgumentException` رد می‌شود.

~~~php
Bale::replyToMessage(
    message: $update['message'],
    text: 'پاسخ شما',
);
~~~

## فوروارد، کپی و action گفتگو

`forwardMessage` و `copyMessage` wrapperهای رسمی جداگانه‌اند. برای نمایش وضعیت موقت گفتگو، مانند شروع آپلود عکس، از `sendChatAction` استفاده کنید.

~~~php
Bale::forwardMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::copyMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::sendChatAction(chatId: '@target', action: 'upload_photo');
~~~

## ویرایش یا حذف پیام

ویرایش متن، زیرنویس یا keyboard و حذف پیام به‌ترتیب methodهای مستقل رسمی هستند. Bale برای نتیجهٔ methodهای edit نوع مشخصی مستند نکرده است؛ Laravel Bale نتیجهٔ خام Bale را بازمی‌گرداند.

~~~php
Bale::editMessageText(chatId: 123456789, messageId: 42, text: 'متن جدید');
Bale::editMessageCaption(chatId: 123456789, messageId: 42, options: ['caption' => 'زیرنویس جدید']);
Bale::editMessageReplyMarkup(chatId: 123456789, messageId: 42, options: ['reply_markup' => $replyMarkup]);
Bale::deleteMessage(chatId: 123456789, messageId: 42);
~~~

برای ساخت keyboard و پاسخ به دکمه‌ها، [راهنمای Callback و Keyboard](callbacks-keyboards.md) را ببینید. امضاها و خروجی همهٔ methodها در [مرجع API](api-reference.md) مرجع نهایی هستند.
