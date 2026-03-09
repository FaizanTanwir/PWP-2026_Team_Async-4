import { IsString, IsNotEmpty, IsNumber } from 'class-validator';

export class CreateSentenceDto {
  @IsString()
  @IsNotEmpty()
  text_target: string;

  @IsString()
  @IsNotEmpty()
  text_source: string;

  @IsNumber()
  @IsNotEmpty()
  unitId: number;
}
