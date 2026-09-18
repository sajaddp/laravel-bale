---
title: پوشش Bale Bot API در Laravel Bale
description: فهرست authoritative متدهای رسمی Bale Bot API که Laravel Bale پشتیبانی می‌کند یا نمی‌کند.
---

# پوشش Bale Bot API

این inventory تمام methodهای Bot API حاضر در HTML رسمی Bale است؛ typeها، MiniApp و JavaScript APIها در این جدول نیستند. «Supported» یعنی wrapper مستقیم public در Laravel Bale وجود دارد. تاریخ roadmap یا سازگاری استنباطی وجود ندارد.

## Core, updates, and webhook

| Bale method | Status | Package method / note |
| --- | --- | --- |
| `getMe` | Supported | `getMe` |
| `getUpdates` | Supported | `getUpdates` |
| `setWebhook` | Supported | `setWebhook` |
| `deleteWebhook` | Supported | `deleteWebhook` |
| `getWebhookInfo` | Supported | `getWebhookInfo` |

## Messaging, media, callback, and review

| Bale method | Status | Package method / note |
| --- | --- | --- |
| `sendMessage` | Supported | `sendMessage` |
| `forwardMessage` | Supported | `forwardMessage` |
| `copyMessage` | Supported | `copyMessage` |
| `sendPhoto` | Supported | `sendPhoto` |
| `sendAudio` | Supported | `sendAudio` |
| `sendDocument` | Supported | `sendDocument` |
| `sendVideo` | Supported | `sendVideo` |
| `sendAnimation` | Supported | `sendAnimation` |
| `sendVoice` | Supported | `sendVoice` |
| `sendMediaGroup` | Supported | `sendMediaGroup` |
| `sendLocation` | Supported | `sendLocation` |
| `sendContact` | Supported | `sendContact` |
| `sendChatAction` | Supported | `sendChatAction` |
| `getFile` | Supported | `getFile` |
| `answerCallbackQuery` | Supported | `answerCallbackQuery` |
| `askReview` | Supported | `askReview` |
| `editMessageText` | Supported | `editMessageText` |
| `editMessageCaption` | Supported | `editMessageCaption` |
| `editMessageReplyMarkup` | Supported | `editMessageReplyMarkup` |
| `deleteMessage` | Supported | `deleteMessage` |

`replyToMessage` و `downloadFile` در این جدول نیستند، چون convenienceهای Laravel Bale هستند، نه Bale methods.

## Chat administration

| Bale method | Status | Note |
| --- | --- | --- |
| `banChatMember` | Unsupported | Not currently implemented |
| `unbanChatMember` | Unsupported | Not currently implemented |
| `promoteChatMember` | Unsupported | Not currently implemented |
| `setChatPhoto` | Unsupported | Not currently implemented |
| `leaveChat` | Unsupported | Not currently implemented |
| `getChat` | Unsupported | Not currently implemented |
| `getChatAdministrators` | Unsupported | Not currently implemented |
| `getChatMembersCount` | Unsupported | Not currently implemented |
| `getChatMember` | Unsupported | Not currently implemented |
| `pinChatMessage` | Unsupported | Not currently implemented |
| `unPinChatMessage` | Unsupported | Not currently implemented |
| `unpinAllChatMessages` | Unsupported | Not currently implemented |
| `setChatTitle` | Unsupported | Not currently implemented |
| `setChatDescription` | Unsupported | Not currently implemented |
| `deleteChatPhoto` | Unsupported | Not currently implemented |
| `createChatInviteLink` | Unsupported | Not currently implemented |
| `revokeChatInviteLink` | Unsupported | Not currently implemented |
| `exportChatInviteLink` | Unsupported | Not currently implemented |

## Stickers and payments

| Bale method | Status | Note |
| --- | --- | --- |
| `uploadStickerFile` | Unsupported | Not currently implemented |
| `createNewStickerSet` | Unsupported | Not currently implemented |
| `addStickerToSet` | Unsupported | Not currently implemented |
| `sendInvoice` | Unsupported | Not currently implemented |
| `createInvoiceLink` | Unsupported | Not currently implemented |
| `answerPreCheckoutQuery` | Unsupported | Not currently implemented |
| `inquireTransaction` | Unsupported | Not currently implemented |

Summary: **25 supported** and **25 unsupported** official Bale methods.
