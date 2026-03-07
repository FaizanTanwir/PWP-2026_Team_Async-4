import {
  Injectable,
  NotFoundException,
  ConflictException,
  InternalServerErrorException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Language } from '../../entities/language.entity';
import { CreateLanguageDto } from './dto/create-language.dto';
import { UpdateLanguageDto } from './dto/update-language.dto';

@Injectable()
export class LanguagesService {
  constructor(
    @InjectRepository(Language)
    private readonly repo: Repository<Language>,
  ) {}

  async create(createLanguageDto: CreateLanguageDto): Promise<Language> {
    try {
      const language = this.repo.create(createLanguageDto);
      return await this.repo.save(language);
    } catch (error) {
      if (error.code === '23505') {
        throw new ConflictException('Language name or code already exists.');
      }
      throw new InternalServerErrorException();
    }
  }

  findAll(): Promise<Language[]> {
    return this.repo.find();
  }

  async findOne(id: number): Promise<Language> {
    const language = await this.repo.findOne({ where: { id } });
    if (!language) throw new NotFoundException(`Language #${id} not found`);
    return language;
  }

  // 2 & 3. Update with duplicate check and database save
  async update(id: number, updateLanguageDto: UpdateLanguageDto): Promise<Language> {
    // Preload merges the existing entity with the new DTO data
    const language = await this.repo.preload({
      id: id,
      ...updateLanguageDto,
    });

    if (!language) throw new NotFoundException(`Language #${id} not found`);

    try {
      // Must await the save to actually commit changes to the DB
      return await this.repo.save(language);
    } catch (error) {
      if (error.code === '23505') {
        throw new ConflictException('New language name or code already exists.');
      }
      throw new InternalServerErrorException();
    }
  }

  async remove(id: number): Promise<Language> {
    const language = await this.findOne(id);
    return await this.repo.remove(language);
  }
}