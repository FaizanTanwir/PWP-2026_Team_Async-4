// src/sentences/sentences.controller.ts
import { Controller, Get, Post, Body, Param, Delete, ParseIntPipe, Patch, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiParam } from '@nestjs/swagger';
import { SentencesService } from './sentences.service';
import { CreateSentenceDto } from './dto/create-sentence.dto';
import { UpdateSentenceDto } from './dto/update-sentence.dto';
import { Sentence } from '../../entities/sentence.entity';
import { JwtAuthGuard } from '../../auth/jwt-auth.guard';

@ApiTags('Sentences')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('sentences')
export class SentencesController {
  constructor(private readonly sentencesService: SentencesService) {}

  @Post()
  @ApiOperation({ 
    summary: 'Create a sentence with auto-translation', 
    description: 'Creates a sentence and automatically generates a word-by-word breakdown in the background.' 
  })
  @ApiResponse({ status: 201, type: Sentence })
  @ApiResponse({ status: 404, description: 'Unit not found' })
  create(@Body() createSentenceDto: CreateSentenceDto) {
    return this.sentencesService.create(createSentenceDto);
  }

  @Get()
  @ApiOperation({ summary: 'Get all sentences with unit and attempt data' })
  @ApiResponse({ status: 200, type: [Sentence] })
  findAll() {
    return this.sentencesService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get details for a single sentence including words' })
  @ApiParam({ name: 'id', example: 1 })
  @ApiResponse({ status: 200, type: Sentence })
  @ApiResponse({ status: 404, description: 'Sentence not found' })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.sentencesService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update sentence text or associated unit' })
  @ApiResponse({ status: 200, type: Sentence })
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() updateSentenceDto: UpdateSentenceDto,
  ) {
    return this.sentencesService.update(id, updateSentenceDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete a sentence and its word links' })
  @ApiResponse({ status: 204, description: 'Removed' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.sentencesService.remove(id);
  }
}