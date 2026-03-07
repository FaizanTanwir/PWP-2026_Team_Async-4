import { IsString, IsInt, MinLength } from 'class-validator';

export class CreateCourseDto {
  @IsString()
  @MinLength(3)
  title: string;

  @IsInt()
  sourceLanguageId: number;

  @IsInt()
  targetLanguageId: number;
}