<?php

declare(strict_types=1);

namespace Sajaddp\Bale\Facades;

use Illuminate\Support\Facades\Facade;
use Sajaddp\Bale\BaleClient;

/**
 * @method static array<mixed> getMe()
 * @method static array<mixed> sendMessage(int|string $chatId, string $text, array<string, mixed> $options = [])
 */
class Bale extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaleClient::class;
    }
}
