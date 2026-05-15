<?php

namespace App\Enums;

/**
 * Defines the access levels within the application.
 */
enum UserRole: string
{
    /** Full system access and management. */
    case ADMIN = 'ADMIN';

    /** Can create courses, units, and review student progress. */
    case TEACHER = 'TEACHER';

    /** Can enroll in courses and submit answers. */
    case STUDENT = 'STUDENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
