---
title: راهنمای AI Coding برای Laravel Bale
description: مرجع فنی برای استفادهٔ AI از Laravel Bale، مرز Bale و Telegram و منابع factual پکیج.
---

# AI Coding با Laravel Bale

این صفحه برای agentها و ابزارهای AI است که روی integration بله کار می‌کنند. Laravel Bale یک integration کم‌حجم Laravel 13 برای Bale Bot API است، نه یک framework ربات و نه یک compatibility layer برای Telegram.

## پیش از تولید کد، منبع درست را انتخاب کنید

- [مرجع API](api-reference.md) فهرست factual API عمومی پکیج و تفکیک wrapper رسمی، convenience و API تست است.
- [پوشش Bale Bot API](api-coverage.md) پاسخ authoritative به پشتیبانی یا نبود پشتیبانی هر method رسمی Bale است.
- [راهنمای تست](testing.md) مرجع fake و assertionهای پکیج است.
- [رفع اشکال](troubleshooting.md) مرزهای failure و assumptionهای خطرناک را ثبت می‌کند.

Laravel Boost و skill `bale-development` در خود repository، همین boundaryها را برای محیط‌های AI-aware بیان می‌کنند.

## قواعد تولید کد

1. هیچ method، option یا field مخصوص Telegram را برای Bale اختراع نکنید.
2. پیش از فرض‌کردن وجود Bale method، [پوشش API](api-coverage.md) را بررسی کنید.
3. wrapperهای رسمی Bale را فقط همان‌طور که در [مرجع API](api-reference.md) آمده‌اند فراخوانی کنید.
4. `replyToMessage` و `downloadFile` convenienceهای Laravel Bale هستند، نه endpointهای رسمی Bale.
5. برای تست رفتار معمول پکیج از `Bale::fake()` استفاده کنید؛ fake مسیر ساخت درخواست و parser واقعی `BaleClient` را حفظ می‌کند.

~~~php
use Sajaddp\Bale\Facades\Bale;

Bale::fake();

Bale::sendMessage(chatId: 123456789, text: 'سلام');

Bale::assertSent('sendMessage', ['chat_id' => 123456789]);
~~~

## مسئولیت‌های application را به پکیج نسبت ندهید

Route دریافت Webhook، controller، polling loop، offset persistence، queue، keyboard builder، callback router و سیاست ذخیره‌سازی فایل در application Laravel قرار دارند. [Webhook](webhooks.md)، [Polling](polling.md) و [فایل و رسانه](files-media.md) این مرز را با مثال‌های محدود و واقعی نشان می‌دهند.

هنگام تردید، از README snippet نتیجه‌گیری نکنید؛ صفحهٔ کوچک و factual متناسب با task را بخوانید.
