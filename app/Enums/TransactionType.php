<?php

namespace App\Enums;

enum TransactionType: string
{
    case Paid = 'paid';
    case Received = 'received';
    case Transferred = 'transferred';

    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
