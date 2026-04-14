// src/units/dto/create-unit.dto.ts
import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, IsNumber } from 'class-validator';

export class CreateUnitDto {
  @ApiProperty({ example: 'Numbers 1-10', description: 'The name of the unit' })
  @IsString()
  @IsNotEmpty()
  title: string;

  @ApiProperty({ example: 1, description: 'The ID of the course this unit belongs to' })
  @IsNumber()
  @IsNotEmpty()
  courseId: number;
}