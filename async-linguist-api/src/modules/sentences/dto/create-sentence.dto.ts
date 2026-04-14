// src/sentences/dto/create-sentence.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, IsNumber, IsOptional } from 'class-validator';

export class CreateSentenceDto {
  @ApiProperty({ 
    required: false, 
    example: 'Minä opiskelen', 
    description: 'Target translation. If omitted, the system will auto-translate from source.' 
  })
  @IsString()
  @IsOptional()
  text_target?: string;

  @ApiProperty({ 
    example: 'I am studying', 
    description: 'The base sentence in the source language' 
  })
  @IsString()
  @IsNotEmpty()
  text_source: string;

  @ApiProperty({ example: 1, description: 'The ID of the Unit this sentence belongs to' })
  @IsNumber()
  @IsNotEmpty()
  unitId: number;
}