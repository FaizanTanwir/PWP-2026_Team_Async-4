import { Controller, Get, Post, Body, Param, Delete, ParseIntPipe, Patch} from '@nestjs/common';
import { SentencesService } from './sentences.service';
import { CreateSentenceDto } from './dto/create-sentence.dto';
import { UpdateSentenceDto } from './dto/update-sentence.dto';

@Controller('sentences')
export class SentencesController {
  constructor(private readonly sentencesService: SentencesService) {}

  @Post()
  create(@Body() createSentenceDto: CreateSentenceDto) {
    return this.sentencesService.create(createSentenceDto);
  }

  @Get()
  findAll() {
    return this.sentencesService.findAll();
  }

  @Get(':id')
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.sentencesService.findOne(id);
  }

  @Patch(':id')
    update(
    @Param('id', ParseIntPipe) id: number, 
    @Body() updateSentenceDto: UpdateSentenceDto
    ) {
    return this.sentencesService.update(id, updateSentenceDto);
    }

  @Delete(':id')
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.sentencesService.remove(id);
  }
}