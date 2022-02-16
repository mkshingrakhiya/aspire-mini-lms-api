<?php

namespace App\Enums;

enum PaymentFrequency: string
{
    case WEEKLY = 'W';
    case MONTHLY = 'M';
    case QUARTERLY = 'Q';
    case YEARLY = 'Y';
}

