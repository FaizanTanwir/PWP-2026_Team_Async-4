<?php

namespace App\Enums;

/**
 * Defines the access levels within the application.
 */
enum UserRole: string
{
    /** Full system access and management. */
    case ADMIN = 'Admin';

    /** Can create courses, units, and review student progress. */
    case TEACHER = 'Teacher';

    /** Can enroll in courses and submit answers. */
    case STUDENT = 'Student';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
