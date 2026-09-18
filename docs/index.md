---
title: مستندات Laravel Bale برای Bale Bot API
description: مرجع فارسی Laravel Bale برای استفاده از Bale Bot API در Laravel 13 و PHP 8.3+.
---

# مستندات Laravel Bale

`sajaddp/laravel-bale` یک client کم‌حجم برای Bale Bot API در Laravel 13 و PHP 8.3+ است. این سایت مرجع ساخت‌یافتهٔ استفاده از پکیج در اکوسیستم لاراول است؛ همهٔ مثال‌ها فقط API واقعی پکیج را نشان می‌دهند و رفتار Bale را از مستندات رسمی بله می‌گیرند.

پکیج هنوز در Packagist منتشر نشده است. [README اصلی](https://github.com/sajaddp/laravel-bale/blob/main/README.md) برای مرور سریع و این سایت برای راهنمای مرحله‌به‌مرحله و مرجع API است.

- [شروع کار](getting-started.md): نصب، پیکربندی و نخستین پیام یا پاسخ.
- [پیام‌ها](messages.md): ارسال، پاسخ، فوروارد، کپی، ویرایش و حذف پیام.
- [Webhook](webhooks.md): دریافت Update در برنامهٔ Laravel 13 و تنظیم Bale.
- [Polling](polling.md): یک درخواست `getUpdates`، offset و مسئولیت application.
- [فایل و رسانه](files-media.md): ارسال، multipart، `file_id` و دانلود امن فایل.
- [Callback و Keyboard](callbacks-keyboards.md): keyboard خام و پاسخ به Callback Query.
- [مرجع API](api-reference.md): امضای تمام APIهای عمومی، با تفکیک wrapper رسمی، convenience و testing.
- [پوشش Bale Bot API](api-coverage.md): پاسخ دقیق به این‌که method رسمی مشخصی پشتیبانی می‌شود یا نه.
- [تست](testing.md): `Bale::fake()` و assertionهای Laravel-native.
- [دستورپخت‌ها](recipes.md): workflowهای رایج با APIهای واقعی.
- [رفع اشکال](troubleshooting.md): خطاها و مرزهای پرتکرار integration.
- [AI Coding](ai-coding.md): مرزهای دقیق برای agentها و ابزارهای AI.

برای مشارکت در خود پکیج، [راهنمای مشارکت](https://github.com/sajaddp/laravel-bale/blob/main/CONTRIBUTING.md) و [سیاست امنیتی](https://github.com/sajaddp/laravel-bale/blob/main/SECURITY.md) را بخوانید.
