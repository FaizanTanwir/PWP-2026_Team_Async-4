// src/attempts/dto/create-attempt.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, IsNumber, IsUrl, Min, Max } from 'class-validator';

export class CreateAttemptDto {
  @ApiProperty({ 
    example: 'https://storage.googleapis.com/audio/attempt123.mp3', 
    description: 'The URL to the recorded audio file' 
  })
  @IsString()
  @IsNotEmpty()
  @IsUrl()
  audio_url: string;

  @ApiProperty({ 
    example: 85, 
    description: 'The AI-generated pronunciation score (0-100)', 
    minimum: 0, 
    maximum: 100 
  })
  @IsNumber()
  @Min(0)
  @Max(100)
  score: number;

  @ApiProperty({ 
    example: 12, 
    description: 'The ID of the sentence being attempted' 
  })
  @IsNumber()
  @IsNotEmpty()
  sentenceId: number;
}