<?php

declare(strict_types=1);

namespace Sajaddp\Bale\Exceptions;

use RuntimeException;

class BaleRequestException extends RuntimeException
{
    /** @param array<string, mixed> $parameters */
    public function __construct(
        public readonly ?int $baleErrorCode,
        public readonly string $description,
        public readonly array $parameters = [],
    ) {
        parent::__construct($description, $baleErrorCode ?? 0);
    }

    /** @param array<string, mixed> $response */
    public static function fromResponse(array $response): self
    {
        $errorCode = $response['error_code'] ?? null;
        $description = $response['description'] ?? 'Bale API request failed.';
        $parameters = $response['parameters'] ?? [];

        return new self(
            baleErrorCode: is_int($errorCode) ? $errorCode : null,
            description: is_string($description) ? $description : 'Bale API request failed.',
            parameters: is_array($parameters) ? $parameters : [],
        );
    }
}
