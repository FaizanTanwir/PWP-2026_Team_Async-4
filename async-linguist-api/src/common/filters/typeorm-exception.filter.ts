import { ExceptionFilter, Catch, ArgumentsHost, ConflictException, HttpStatus } from '@nestjs/common';
import { QueryFailedError } from 'typeorm';

// We define these ourselves so we don't need external packages
enum PostgresErrorCode {
  UniqueViolation = '23505',
  ForeignKeyViolation = '23503',
}

@Catch(QueryFailedError)
export class TypeOrmExceptionFilter implements ExceptionFilter {
  catch(exception: any, host: ArgumentsHost) {
    const response = host.switchToHttp().getResponse();

    // Check for Unique Constraint (Duplicate name/code)
    if (exception.code === PostgresErrorCode.UniqueViolation) {
      return response.status(HttpStatus.CONFLICT).json({
        statusCode: HttpStatus.CONFLICT,
        message: 'This record already exists.',
        error: 'Conflict',
      });
    }

    // Check for Foreign Key Violation (Deleting a language used by a course)
    if (exception.code === PostgresErrorCode.ForeignKeyViolation) {
      return response.status(HttpStatus.BAD_REQUEST).json({
        statusCode: HttpStatus.BAD_REQUEST,
        message: 'This record is in use and cannot be modified or removed.',
        error: 'Bad Request',
      });
    }

    // Standard Fallback
    return response.status(500).json({
      statusCode: 500,
      message: 'Internal server error',
    });
  }
}