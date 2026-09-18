---
title: ارسال و مدیریت پیام بله در Laravel
description: راهنمای ارسال، پاسخ، فوروارد، کپی، ویرایش، حذف و نمایش وضعیت پیام بله با Laravel Bale.
---

# چطور در Laravel به بله پیام بفرستیم؟

برای ارسال متن از متد رسمی `sendMessage` استفاده کنید. شناسهٔ گفتگو می‌تواند عدد صحیح یا شناسهٔ رشته‌ای مستند Bale باشد.

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::sendMessage(
    chatId: 123456789,
    text: 'سلام از Laravel',
);
~~~

پارامترهای الزامی متد بر کلید همنام در `options` مقدم‌اند. فقط گزینه‌هایی را بفرستید که Bale برای همان متد API مستند کرده است.

## پاسخ به پیام

روش رسمی، ارسال `reply_to_message_id` با `sendMessage` است:

~~~php
Bale::sendMessage(
    chatId: $message['chat']['id'],
    text: 'پاسخ شما',
    options: ['reply_to_message_id' => $message['message_id']],
);
~~~

`replyToMessage` متد کمکی پکیج برای همین روند تکراری است. ورودی آن باید دادهٔ خام Bale Message با `message_id` و `chat.id` عددی باشد؛ ورودی ناقص پیش از هر درخواست HTTP با `InvalidArgumentException` رد می‌شود.

~~~php
Bale::replyToMessage(
    message: $update['message'],
    text: 'پاسخ شما',
);
~~~

## فوروارد، کپی و وضعیت گفتگو

`forwardMessage` و `copyMessage` متدهای رسمی جداگانه‌اند. برای نمایش وضعیت موقت گفتگو، مانند آغاز آپلود عکس، از `sendChatAction` استفاده کنید.

~~~php
Bale::forwardMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::copyMessage(chatId: '@target', fromChatId: 123456789, messageId: 42);
Bale::sendChatAction(chatId: '@target', action: 'upload_photo');
~~~

## ویرایش یا حذف پیام

ویرایش متن، زیرنویس یا دکمه‌ها و حذف پیام، هر کدام متد رسمی مستقلی دارند. Bale برای نتیجهٔ متدهای ویرایش نوع مشخصی مستند نکرده است؛ Laravel Bale نتیجهٔ خام Bale را بازمی‌گرداند.

~~~php
Bale::editMessageText(chatId: 123456789, messageId: 42, text: 'متن جدید');
Bale::editMessageCaption(chatId: 123456789, messageId: 42, options: ['caption' => 'زیرنویس جدید']);
Bale::editMessageReplyMarkup(chatId: 123456789, messageId: 42, options: ['reply_markup' => $replyMarkup]);
Bale::deleteMessage(chatId: 123456789, messageId: 42);
~~~

برای ساخت دکمه‌ها و پاسخ به آن‌ها، [راهنمای Callback و دکمه‌ها](callbacks-keyboards.md) را ببینید. امضا و خروجی همهٔ متدها در [مرجع API](api-reference.md) مرجع نهایی است.
