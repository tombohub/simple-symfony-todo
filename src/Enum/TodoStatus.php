<?php

namespace App\Enum;

enum TodoStatus: string
{
    case DONE = 'Done';
    case NOT_DONE = 'Not Done';
}
