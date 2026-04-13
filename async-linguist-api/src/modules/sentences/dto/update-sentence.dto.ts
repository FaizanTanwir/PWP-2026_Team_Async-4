// src/sentences/dto/update-sentence.dto.ts
import { ApiProperty, PartialType } from '@nestjs/swagger';
import { CreateSentenceDto } from './create-sentence.dto';
import { IsNotEmpty, IsString } from 'class-validator';

export class UpdateSentenceDto extends PartialType(CreateSentenceDto) {
  @ApiProperty()
  @IsString()
  @IsNotEmpty()
  text_target: string;
}