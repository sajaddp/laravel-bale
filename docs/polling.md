---
title: Polling بله با Laravel Bale
description: راهنمای getUpdates بله در Laravel، offset، limit، timeout و مسئولیت loop و persistence برنامه.
---

# چطور Updateهای بله را با Polling دریافت کنیم؟

`getUpdates` دقیقاً یک درخواست به Bale می‌سازد و array نتیجه را برمی‌گرداند. پکیج daemon، loop بی‌نهایت، queue، handler یا persistence برای offset ندارد.

~~~php
use Sajaddp\Bale\Facades\Bale;

$updates = Bale::getUpdates([
    'offset' => $nextOffset,
    'limit' => 100,
    'timeout' => 30,
]);
~~~

## offset را در application نگه دارید

پس از پردازش موفق هر Update، برنامهٔ شما باید مقدار offset بعدی را در storage مناسب خود نگه دارد. این تصمیم به مدل پردازش، تحمل خطا و زیرساخت application بستگی دارد؛ Laravel Bale state پنهان ایجاد نمی‌کند.

~~~php
foreach ($updates as $update) {
    // پردازش idempotent برنامهٔ خودتان

    $nextOffset = $update['update_id'] + 1;
}

// $nextOffset را فقط پس از سیاست پردازش خودتان persist کنید.
~~~

## timeout و headroom HTTP

گزینهٔ `timeout` برای long polling به Bale می‌رود. Laravel Bale برای timeout HTTP headroom مناسب اضافه می‌کند تا درخواست HTTP پیش از timeout مورد انتظار Bale قطع نشود؛ این به‌معنای مدیریت loop یا retry توسط پکیج نیست.

برنامهٔ شما باید چرخهٔ فراخوانی، زمان‌بندی، backoff، logging و نحوهٔ توقف worker را مالک باشد. اگر نمی‌خواهید چنین lifecycleای را نگه دارید، [Webhook در Laravel 13](webhooks.md) مسیر push-based را توضیح می‌دهد.

برای بررسی وجود wrapperهای Bale، نه ساخت method فرضی، همیشه [پوشش Bale Bot API](api-coverage.md) را بررسی کنید.
