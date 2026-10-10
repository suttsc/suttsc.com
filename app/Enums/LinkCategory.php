<?php

declare(strict_types=1);

namespace App\Enums;

enum LinkCategory: string
{
    case Architecture = 'architecture';
    case Laravel = 'laravel';
    case Leadership = 'leadership';
    case Design = 'design';
    case Testing = 'testing';
    case Other = 'other';
}
