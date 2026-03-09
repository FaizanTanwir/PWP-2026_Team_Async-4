import { IsString, IsNotEmpty, IsNumber, IsUrl, Min, Max } from 'class-validator';

export class CreateAttemptDto {
  @IsString()
  @IsNotEmpty()
  @IsUrl() // Ensures the audio link is a valid URL
  audio_url: string;

  @IsNumber()
  @Min(0)
  @Max(100)
  score: number;

  @IsNumber()
  @IsNotEmpty()
  sentenceId: number;
}