// src/units/units.controller.ts
import { Controller, Get, Post, Body, Patch, Param, Delete, ParseIntPipe, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiParam } from '@nestjs/swagger';
import { UnitsService } from './units.service';
import { CreateUnitDto } from './dto/create-unit.dto';
import { UpdateUnitDto } from './dto/update-unit.dto';
import { Unit } from '../../entities/unit.entity';
import { JwtAuthGuard } from '../../auth/jwt-auth.guard';

@ApiTags('Curriculum / Units')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('units')
export class UnitsController {
  constructor(private readonly unitsService: UnitsService) {}

  @Post()
  @ApiOperation({ summary: 'Create a new learning unit' })
  @ApiResponse({ status: 201, description: 'Unit created successfully.', type: Unit })
  create(@Body() createUnitDto: CreateUnitDto) {
    return this.unitsService.create(createUnitDto);
  }

  @Get()
  @ApiOperation({ summary: 'List all units with their course and sentence data' })
  @ApiResponse({ status: 200, type: [Unit] })
  findAll() {
    return this.unitsService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get details for a specific unit' })
  @ApiParam({ name: 'id', example: 1 })
  @ApiResponse({ status: 200, type: Unit })
  @ApiResponse({ status: 404, description: 'Unit not found' })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.unitsService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update unit title or move it to a different course' })
  @ApiResponse({ status: 200, type: Unit })
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() updateUnitDto: UpdateUnitDto,
  ) {
    return this.unitsService.update(id, updateUnitDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete a unit and all its sentences (Cascade)' })
  @ApiResponse({ status: 204, description: 'Unit removed' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.unitsService.remove(id);
  }
}