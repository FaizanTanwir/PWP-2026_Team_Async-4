import { IsString, IsNotEmpty, MinLength } from 'class-validator';

export class CreateLanguageDto {
  @IsString()
  @IsNotEmpty()
  @MinLength(2, { message: 'Language name must be at least 2 characters long' })
  name: string; // e.g., "Finnish" or "Urdu"

  @IsString()
  @IsNotEmpty()
  @MinLength(2)
  code: string; // e.g., "fi" or "ur"
}