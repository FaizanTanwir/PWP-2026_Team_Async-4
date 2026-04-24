<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'Admin';
    case TEACHER = 'Teacher';
    case STUDENT = 'Student';

    // Helper to get all values for seeding
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
