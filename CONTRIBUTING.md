# مشارکت در Laravel Bale

از مشارکت شما استقبال می‌شود. این پکیج Laravel 13 و PHP 8.3+ را هدف می‌گیرد و عمداً یک client کم‌حجم باقی می‌ماند.

پیش از تغییر رفتار Bale، HTML مستندات رسمی Bale را بررسی کنید. از شباهت نام‌ها با Telegram، API یا contract استنباط نکنید. abstraction آینده‌نگر، framework جدید یا convenience method تازه فقط وقتی پذیرفتنی است که workflow تکراری و خطاپذیر را با primitives موجود compose کند.

برای تغییر کد:

1. تست observable با Laravel HTTP fake اضافه یا به‌روزرسانی کنید؛ شبکهٔ واقعی نزنید.
2. `composer test` را اجرا کنید.
3. اگر public API تغییر کرده است، README، PHPDoc Facade، Boost guideline و skill و [مرجع API](docs/api-reference.md) را همگام کنید.
4. اگر wrapper رسمی اضافه شده است، [پوشش API](docs/api-coverage.md) را بر اساس مستندات Bale اصلاح کنید.

مسائل امنیتی را در issue عمومی مطرح نکنید؛ [SECURITY.md](SECURITY.md) را ببینید. برای جهت‌گیری package به [مستندات](docs/README.md) و برای checklist PR به [template](.github/PULL_REQUEST_TEMPLATE.md) مراجعه کنید.
