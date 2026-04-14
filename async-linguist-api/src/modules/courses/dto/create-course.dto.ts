// src/courses/dto/create-course.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsInt, IsNotEmpty } from 'class-validator';

export class CreateCourseDto {
  @ApiProperty({ example: 'Finnish for Beginners', description: 'The title of the course' })
  @IsString()
  @IsNotEmpty()
  title: string;

  @ApiProperty({ example: 1, description: 'ID of the language you are learning from' })
  @IsInt()
  sourceLanguageId: number;

  @ApiProperty({ example: 2, description: 'ID of the language you want to learn' })
  @IsInt()
  targetLanguageId: number;

  @ApiProperty({ example: 1, description: 'The ID of the user/teacher creating this course' })
  @IsInt()
  @IsNotEmpty()
  createdById: number;
}