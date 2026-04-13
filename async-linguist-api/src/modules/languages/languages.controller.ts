// src/languages/languages.controller.ts
import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  ParseIntPipe,
  HttpCode,
  HttpStatus,
  UseInterceptors, // Added back for the commented line
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiParam, ApiBody } from '@nestjs/swagger';
import { LanguagesService } from './languages.service';
import { CreateLanguageDto } from './dto/create-language.dto';
import { UpdateLanguageDto } from './dto/update-language.dto';
import { Language } from '../../entities/language.entity';
import { CacheInterceptor } from '@nestjs/cache-manager';

@ApiTags('Languages')
@Controller('languages')
export class LanguagesController {
  constructor(private readonly languagesService: LanguagesService) {}

  @Post()
  @ApiOperation({ summary: 'Register a new language' })
  @ApiBody({ type: CreateLanguageDto }) // Explicitly define request body
  @ApiResponse({ 
    status: 201, 
    description: 'Language created successfully.', 
    type: Language 
  })
  @ApiResponse({ 
    status: 400, 
    description: 'Validation failed or Duplicate entry',
    schema: {
      example: {
        statusCode: 400,
        message: ['The name has already been taken.', 'The code has already been taken.'],
        error: 'Bad Request'
      }
    }
  })
  create(@Body() createLanguageDto: CreateLanguageDto) {
    return this.languagesService.create(createLanguageDto);
  }

  @Get()
  // @UseInterceptors(CacheInterceptor)
  @ApiOperation({ summary: 'Get all supported languages' })
  @ApiResponse({ 
    status: 200, 
    description: 'Return list of languages.', 
    type: [Language] // Square brackets ensure Swagger shows an ARRAY example
  })
  findAll() {
    return this.languagesService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get language details by ID' })
  @ApiParam({ name: 'id', description: 'The unique ID of the language', example: 1 })
  @ApiResponse({ 
    status: 200, 
    description: 'Language found.', 
    type: Language 
  })
  @ApiResponse({ 
    status: 404, 
    description: 'Language not found.',
    schema: { example: { statusCode: 404, message: 'Language #1 not found' } } 
  })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.languagesService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update language metadata' })
  @ApiBody({ type: UpdateLanguageDto }) // Ensures the optional fields show in Swagger
  @ApiResponse({ 
    status: 200, 
    description: 'Language updated.', 
    type: Language 
  })
  @ApiResponse({ 
    status: 404, 
    description: 'Language not found.',
    schema: { example: { statusCode: 404, message: 'Language #1 not found' } }
  })
  update(
    @Param('id', ParseIntPipe) id: number, 
    @Body() updateLanguageDto: UpdateLanguageDto
  ) {
    return this.languagesService.update(id, updateLanguageDto);
  }

  @Delete(':id')
  @HttpCode(HttpStatus.NO_CONTENT)
  @ApiOperation({ summary: 'Remove a language' })
  @ApiParam({ name: 'id', example: 1 })
  @ApiResponse({ status: 204, description: 'Successfully removed.' })
  @ApiResponse({ 
    status: 404, 
    description: 'Language not found.',
    schema: { example: { statusCode: 404, message: 'Language #1 not found' } }
  })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.languagesService.remove(id);
  }
}