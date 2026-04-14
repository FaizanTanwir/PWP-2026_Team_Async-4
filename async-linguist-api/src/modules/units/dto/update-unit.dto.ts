// src/units/dto/update-unit.dto.ts
import { ApiProperty, PartialType } from '@nestjs/swagger'; // Switch to Swagger PartialType
import { CreateUnitDto } from './create-unit.dto';

export class UpdateUnitDto extends PartialType(CreateUnitDto) {}