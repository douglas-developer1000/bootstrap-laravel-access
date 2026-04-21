<?php

namespace App\Libraries\Enums;

enum TimestampsColumnsEnum: string
{
    case CREATED_AT = 'created_at';
    case UPDATED_AT = 'updated_at';
    case NONE = 'none';
    case BOTH = 'both';
}
