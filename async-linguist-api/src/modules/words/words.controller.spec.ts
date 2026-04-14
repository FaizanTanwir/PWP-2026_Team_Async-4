// src/words/words.controller.ts
import { Controller, Get, Post, Body, Patch, Param, Delete, ParseIntPipe, UseGuards } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiParam, ApiBearerAuth } from '@nestjs/swagger';
import { WordsService } from './words.service';
import { CreateWordDto } from './dto/create-word.dto';
import { UpdateWordDto } from './dto/update-word.dto';
import { Word } from '../../entities/word.entity';
import { JwtAuthGuard } from 'src/auth/jwt-auth.guard';

@ApiTags('Dictionary / Words')
@ApiBearerAuth()
@UseGuards(JwtAuthGuard)
@Controller('words')
export class WordsController {
  constructor(private readonly wordsService: WordsService) {}

  @Post()
  @ApiOperation({ summary: 'Add a new word to the dictionary' })
  @ApiResponse({ status: 201, description: 'Word created.', type: Word })
  @ApiResponse({ status: 401, description: 'Unauthorized' })
  create(@Body() createWordDto: CreateWordDto) {
    return this.wordsService.create(createWordDto);
  }

  @Get()
  @ApiOperation({ summary: 'List all words in the system' })
  @ApiResponse({ status: 200, description: 'Successful', type: [Word] })
  findAll() {
    return this.wordsService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get specific word details' })
  @ApiParam({ name: 'id', example: 1 })
  @ApiResponse({ status: 200, type: Word })
  @ApiResponse({ status: 404, description: 'Word not found' })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.wordsService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update a word or its translation' })
  @ApiResponse({ status: 200, type: Word })
  @ApiResponse({ status: 404, description: 'Word not found' })
  update(@Param('id', ParseIntPipe) id: number, @Body() updateWordDto: UpdateWordDto) {
    return this.wordsService.update(id, updateWordDto);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete a word from the dictionary' })
  @ApiResponse({ status: 204, description: 'Deleted successfully' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.wordsService.remove(id);
  }
}