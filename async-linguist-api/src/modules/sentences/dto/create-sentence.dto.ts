// src/sentences/dto/create-sentence.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, IsNumber, IsOptional } from 'class-validator';

export class CreateSentenceDto {
  @ApiProperty({ required: false })
  @IsString()
  @IsOptional()
  text_target?: string; // Optional: AI will fill this if missing

  @ApiProperty({ required: true })
  @IsString()
  @IsNotEmpty()
  text_source: string; // Required: The base for translation

  @IsNumber()
  @IsNotEmpty()
  unitId: number;
}