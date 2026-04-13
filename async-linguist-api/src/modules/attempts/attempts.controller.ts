// src/attempts/attempts.controller.ts
import { Controller, Get, Post, Body, Param, Delete, ParseIntPipe, HttpStatus, HttpCode } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiParam } from '@nestjs/swagger';
import { AttemptsService } from './attempts.service';
import { CreateAttemptDto } from './dto/create-attempt.dto';
import { Attempt } from '../../entities/attempt.entity';

@ApiTags('Attempts')
@Controller('attempts')
export class AttemptsController {
  constructor(private readonly attemptsService: AttemptsService) {}

  @Post()
  @ApiOperation({ summary: 'Submit a new speech attempt' })
  // Fix 1.4 & 1.6: Link to the Entity and DTO
  @ApiResponse({ 
    status: 201, 
    description: 'Attempt created', 
    type: Attempt 
  })
  @ApiResponse({ 
    status: 400, 
    description: 'Validation Error',
    schema: {
      example: {
        statusCode: 400,
        message: ['score must be no greater than 100', 'audio_url must be a URL'],
        error: 'Bad Request'
      }
    }
  })
  create(@Body() createAttemptDto: CreateAttemptDto) {
    return this.attemptsService.create(createAttemptDto);
  }

  @Get()
  @ApiOperation({ summary: 'Retrieve all attempts' })
  // Fix 1.4: Use type: [Attempt] to show an ARRAY example
  @ApiResponse({ 
    status: 200, 
    description: 'Success', 
    type: [Attempt] 
  })
  findAll() {
    return this.attemptsService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get a specific attempt' })
  @ApiResponse({ status: 200, type: Attempt })
  @ApiResponse({ 
    status: 404, 
    description: 'Not Found',
    schema: {
      example: { statusCode: 404, message: 'Attempt with ID 1 not found' }
    }
  })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.attemptsService.findOne(id);
  }

  @Delete(':id')
  @HttpCode(HttpStatus.NO_CONTENT)
  @ApiOperation({ summary: 'Delete attempt' })
  @ApiResponse({ status: 204, description: 'No Content' })
  @ApiResponse({ status: 404, description: 'Not Found' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.attemptsService.remove(id);
  }
}