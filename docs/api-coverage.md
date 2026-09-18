---
title: پوشش Bale Bot API در Laravel Bale
description: فهرست قطعی متدهای رسمی Bale Bot API که Laravel Bale پشتیبانی می‌کند یا نمی‌کند.
---

# پوشش Bale Bot API

این فهرست همهٔ متدهای Bot API موجود در HTML رسمی Bale را در بر می‌گیرد؛ نوع‌ها، MiniApp و APIهای JavaScript در این جدول نیستند. «پشتیبانی‌شده» یعنی متدی عمومی و مستقیم در Laravel Bale وجود دارد. هیچ زمان‌بندی انتشار یا سازگاری استنباطی مطرح نیست.

## بخش پایه، Updateها و وب‌هوک

| متد Bale | وضعیت | متد پکیج / توضیح |
| --- | --- | --- |
| `getMe` | پشتیبانی‌شده | `getMe` |
| `getUpdates` | پشتیبانی‌شده | `getUpdates` |
| `setWebhook` | پشتیبانی‌شده | `setWebhook` |
| `deleteWebhook` | پشتیبانی‌شده | `deleteWebhook` |
| `getWebhookInfo` | پشتیبانی‌شده | `getWebhookInfo` |

## پیام، رسانه، Callback و نظر

| متد Bale | وضعیت | متد پکیج / توضیح |
| --- | --- | --- |
| `sendMessage` | پشتیبانی‌شده | `sendMessage` |
| `forwardMessage` | پشتیبانی‌شده | `forwardMessage` |
| `copyMessage` | پشتیبانی‌شده | `copyMessage` |
| `sendPhoto` | پشتیبانی‌شده | `sendPhoto` |
| `sendAudio` | پشتیبانی‌شده | `sendAudio` |
| `sendDocument` | پشتیبانی‌شده | `sendDocument` |
| `sendVideo` | پشتیبانی‌شده | `sendVideo` |
| `sendAnimation` | پشتیبانی‌شده | `sendAnimation` |
| `sendVoice` | پشتیبانی‌شده | `sendVoice` |
| `sendMediaGroup` | پشتیبانی‌شده | `sendMediaGroup` |
| `sendLocation` | پشتیبانی‌شده | `sendLocation` |
| `sendContact` | پشتیبانی‌شده | `sendContact` |
| `sendChatAction` | پشتیبانی‌شده | `sendChatAction` |
| `getFile` | پشتیبانی‌شده | `getFile` |
| `answerCallbackQuery` | پشتیبانی‌شده | `answerCallbackQuery` |
| `askReview` | پشتیبانی‌شده | `askReview` |
| `editMessageText` | پشتیبانی‌شده | `editMessageText` |
| `editMessageCaption` | پشتیبانی‌شده | `editMessageCaption` |
| `editMessageReplyMarkup` | پشتیبانی‌شده | `editMessageReplyMarkup` |
| `deleteMessage` | پشتیبانی‌شده | `deleteMessage` |

`replyToMessage` و `downloadFile` در این جدول نیستند، چون متدهای کمکی Laravel Bale هستند، نه متدهای Bale.

## مدیریت گفتگو

| متد Bale | وضعیت | توضیح |
| --- | --- | --- |
| `banChatMember` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `unbanChatMember` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `promoteChatMember` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `setChatPhoto` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `leaveChat` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `getChat` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `getChatAdministrators` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `getChatMembersCount` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `getChatMember` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `pinChatMessage` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `unPinChatMessage` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `unpinAllChatMessages` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `setChatTitle` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `setChatDescription` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `deleteChatPhoto` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `createChatInviteLink` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `revokeChatInviteLink` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `exportChatInviteLink` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |

## استیکرها و پرداخت‌ها

| متد Bale | وضعیت | توضیح |
| --- | --- | --- |
| `uploadStickerFile` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `createNewStickerSet` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `addStickerToSet` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `sendInvoice` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `createInvoiceLink` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `answerPreCheckoutQuery` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |
| `inquireTransaction` | پشتیبانی‌نشده | در حال حاضر پیاده‌سازی نشده است |

جمع‌بندی: **۲۵ متد رسمی پشتیبانی‌شده** و **۲۵ متد رسمی پشتیبانی‌نشده** در Bale.
