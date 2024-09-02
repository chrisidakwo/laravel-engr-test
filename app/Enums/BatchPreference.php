<?php

declare(strict_types=1);

namespace App\Enums;

enum BatchPreference: string
{
    case ENCOUNTER_DATE = 'encounter_date';
    case ORDER_RECEIPT_DATE = 'order_receipt_date';
}
