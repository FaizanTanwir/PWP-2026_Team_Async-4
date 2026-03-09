import { PartialType } from '@nestjs/mapped-types';
import { CreateCourseDto } from './create-course.dto';

// PartialType makes all fields in CreateCourseDto optional for PATCH requests
export class UpdateCourseDto extends PartialType(CreateCourseDto) {}
