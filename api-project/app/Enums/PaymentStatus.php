<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Escrowed = 'escrowed';
    case Released = 'released';
    case Refunded = 'refunded';
    case Failed = 'failed';

    public static function values(): array
    {
        return array_map(fn (self $s) => $s->value, self::cases());
    }
}
