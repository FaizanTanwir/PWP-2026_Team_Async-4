// src/words/dto/create-word.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, IsOptional } from 'class-validator';

export class CreateWordDto {
  @ApiProperty({ example: 'arbeiten', description: 'The actual word' })
  @IsString()
  @IsNotEmpty()
  term: string;

  @ApiProperty({ example: 'arbeiten', required: false })
  @IsString()
  @IsOptional()
  lemma?: string;

  @ApiProperty({ example: 'to work', required: false })
  @IsString()
  @IsOptional()
  translation?: string;
}
