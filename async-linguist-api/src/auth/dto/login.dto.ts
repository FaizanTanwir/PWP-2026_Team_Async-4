// src/auth/dto/login.dto.ts
// For now, this is identical to RegisterDto, but keeping them separate 
// is good practice as login requirements might diverge later.
import { ApiProperty } from '@nestjs/swagger';
import { IsEmail, IsNotEmpty } from 'class-validator';

export class LoginDto {
  @ApiProperty({ example: 'admin@linguist.com' })
  @IsEmail()
  email: string;

  @ApiProperty({ example: 'password' })
  @IsNotEmpty()
  password: string;
}