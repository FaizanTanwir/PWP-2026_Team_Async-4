import { IsString, IsInt, MinLength, IsNotEmpty } from 'class-validator';

export class CreateCourseDto {
  @IsNotEmpty()
  @IsString()
  @MinLength(3)
  title: string;

  @IsNotEmpty()
  @IsInt()
  sourceLanguageId: number;
  
  @IsNotEmpty()
  @IsInt()
  targetLanguageId: number; 
}