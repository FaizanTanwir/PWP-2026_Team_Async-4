import { Controller, Get, UseInterceptors } from '@nestjs/common';
import { CacheInterceptor } from '@nestjs/cache-manager'; // Import from here now
import { LanguagesService } from './languages.service';

@Controller('languages')
export class LanguagesController {
  constructor(private readonly service: LanguagesService) {}

  @Get()
  @UseInterceptors(CacheInterceptor)
  async getAll() {
    return await this.service.findAll();
  }
}