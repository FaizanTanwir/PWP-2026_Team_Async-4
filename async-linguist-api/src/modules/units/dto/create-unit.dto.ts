import { IsString, IsNotEmpty, IsNumber } from 'class-validator';

export class CreateUnitDto {
  @IsString()
  @IsNotEmpty()
  title: string;

  @IsNumber()
  @IsNotEmpty()
  courseId: number;
}
