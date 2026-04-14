// src/languages/dto/create-language.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, MinLength } from 'class-validator';

export class CreateLanguageDto {
  @ApiProperty({ example: 'Finnish', description: 'The full name of the language' })
  @IsString()
  @IsNotEmpty()
  @MinLength(2)
  name: string;

  @ApiProperty({ example: 'fi', description: 'The ISO 639-1 two-letter language code' })
  @IsString()
  @IsNotEmpty()
  @MinLength(2)
  code: string;
}