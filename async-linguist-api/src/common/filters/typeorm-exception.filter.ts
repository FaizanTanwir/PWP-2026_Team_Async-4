import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpStatus,
} from '@nestjs/common';
import { QueryFailedError } from 'typeorm';

enum PostgresErrorCode {
  UniqueViolation = '23505',
  ForeignKeyViolation = '23503',
}

interface TypeORMError {
  code?: string;
  detail?: string;
  status?: number;
}

@Catch(QueryFailedError)
export class TypeOrmExceptionFilter implements ExceptionFilter {
  catch(exception: any, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse();

    // 1. Handle Unique Violations (Duplicate name/code)
    if (exception.code === PostgresErrorCode.UniqueViolation) {
      const detail = exception.detail; // e.g., "Key (name)=(Finnish) already exists."

      // Use regex to extract the field name inside the parentheses
      const fieldMatch = detail.match(/\((.*?)\)/);
      const field = fieldMatch ? fieldMatch[1] : 'field';

      return response.status(HttpStatus.CONFLICT).json({
        statusCode: HttpStatus.CONFLICT,
        message: `The ${field} has already been taken.`,
        error: 'Conflict',
        target: field, // Helpful for frontend to highlight the input red
      });
    }

    // 2. Handle Foreign Key Violations
    if (exception.code === PostgresErrorCode.ForeignKeyViolation) {
      return response.status(HttpStatus.BAD_REQUEST).json({
        statusCode: HttpStatus.BAD_REQUEST,
        message:
          'This record is currently in use and cannot be modified or removed.',
        error: 'Bad Request',
      });
    }

    // Default Fallback
    return response.status(500).json({
      statusCode: 500,
      message: 'Internal server error',
    });
  }
}
