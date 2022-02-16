<?php

namespace App\Enums;

enum LoanStatus: int
{
    case PROCESSING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
}

