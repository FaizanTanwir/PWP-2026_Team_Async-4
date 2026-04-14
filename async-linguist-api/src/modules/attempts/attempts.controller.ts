// src/attempts/attempts.controller.ts
import { Controller, Get, Post, Body, Param, Delete, ParseIntPipe, HttpStatus, HttpCode, UseGuards, Request } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiParam, ApiBearerAuth } from '@nestjs/swagger';
import { AttemptsService } from './attempts.service';
import { CreateAttemptDto } from './dto/create-attempt.dto';
import { Attempt } from '../../entities/attempt.entity';
import { JwtAuthGuard } from '../../auth/jwt-auth.guard';

@ApiTags('User Performance / Attempts')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('attempts')
export class AttemptsController {
  constructor(private readonly attemptsService: AttemptsService) {}

  @Post()
  @ApiOperation({ 
    summary: 'Submit a speech attempt', 
    description: 'Saves the score and audio URL for a specific sentence. In a real scenario, the userId should be extracted from the JWT.' 
  })
  @ApiResponse({ status: 201, description: 'Attempt recorded.', type: Attempt })
  @ApiResponse({ status: 400, description: 'Invalid score or URL format.' })
  @ApiResponse({ status: 401, description: 'Unauthorized.' })
  create(@Body() createAttemptDto: CreateAttemptDto, @Request() req) {
    return this.attemptsService.create(createAttemptDto, req.user.userId);
  }

  @Get()
  @ApiOperation({ summary: 'List all attempts (Admin or Debug view)' })
  @ApiResponse({ status: 200, type: [Attempt] })
  findAll() {
    return this.attemptsService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get details of a specific attempt' })
  @ApiParam({ name: 'id', example: 1 })
  @ApiResponse({ status: 200, type: Attempt })
  @ApiResponse({ status: 404, description: 'Attempt not found' })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.attemptsService.findOne(id);
  }

  @Delete(':id')
  @HttpCode(HttpStatus.NO_CONTENT)
  @ApiOperation({ summary: 'Remove an attempt record' })
  @ApiResponse({ status: 204, description: 'Record deleted.' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.attemptsService.remove(id);
  }
}