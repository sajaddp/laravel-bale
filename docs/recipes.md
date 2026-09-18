# دستورپخت‌ها

همهٔ مثال‌ها از API واقعی پکیج استفاده می‌کنند. برای امضاها به [مرجع API](api-reference.md) مراجعه کنید.

## Send a message

~~~php
Bale::sendMessage(chatId: 123456789, text: 'سلام');
~~~

## Reply to a Message

~~~php
Bale::replyToMessage($update['message'], 'پاسخ شما');
~~~

## Inline Keyboard + Callback Query

~~~php
Bale::sendMessage(
    chatId: 123456789,
    text: 'انتخاب کنید',
    options: ['reply_markup' => ['inline_keyboard' => [
        [['text' => 'تأیید', 'callback_data' => 'confirm']],
    ]]],
);

Bale::answerCallbackQuery(callbackQueryId: $update['callback_query']['id']);
~~~

## Upload a local file

~~~php
Bale::sendDocument(
    chatId: 123456789,
    document: new SplFileInfo(storage_path('app/report.pdf')),
);
~~~

## Reuse file_id

~~~php
Bale::sendDocument(chatId: 123456789, document: 'bale-file-id');
~~~

## Download and save a file

~~~php
use Illuminate\Support\Facades\Storage;

Storage::put('bale/report.pdf', Bale::downloadFile($fileId));
~~~

## Send a media group

~~~php
Bale::sendMediaGroup(
    chatId: 123456789,
    media: [
        ['type' => 'photo', 'media' => 'attach://first'],
        ['type' => 'photo', 'media' => 'attach://second'],
    ],
    attachments: [
        'first' => new SplFileInfo(storage_path('app/first.jpg')),
        'second' => new SplFileInfo(storage_path('app/second.jpg')),
    ],
);
~~~

## Polling

~~~php
$updates = Bale::getUpdates(['offset' => $nextOffset, 'timeout' => 30]);
~~~

این یک درخواست است؛ loop و ذخیرهٔ offset با برنامهٔ شماست.

## Webhook in Laravel 13

~~~php
// routes/web.php
Route::post('/bale/webhook', fn (\Illuminate\Http\Request $request) => response()->noContent());

// سپس در bootstrap/app.php همان URI را از CSRF مستثنا کنید.
Bale::setWebhook('https://example.test/bale/webhook');
~~~

برای جزئیات 419 به [رفع اشکال](troubleshooting.md) مراجعه کنید. پکیج route یا controller نمی‌سازد.

## askReview

~~~php
Bale::askReview(userId: 123456789, delaySeconds: 30);
~~~

## Test a Bale integration

~~~php
Bale::fake();

Bale::sendMessage(chatId: 123456789, text: 'سلام');

Bale::assertSent('sendMessage', ['text' => 'سلام']);
~~~

جزئیات در [راهنمای تست](testing.md) است.
