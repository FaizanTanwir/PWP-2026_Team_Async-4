import { IsString, IsNotEmpty, IsOptional } from 'class-validator';

export class CreateWordDto {
  @IsString()
  @IsNotEmpty()
  term: string;

  @IsString()
  @IsOptional()
  lemma?: string;

  @IsString()
  @IsOptional()
  translation?: string;
}
