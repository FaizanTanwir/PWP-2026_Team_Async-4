import { Injectable, NotFoundException } from '@nestjs/common';
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

  async create(dto: CreateLanguageDto): Promise<Language> {
    const language = this.repo.create(dto);
    return await this.repo.save(language);
  }

  findAll(): Promise<Language[]> {
    return this.repo.find();
  }

  async findOne(id: number): Promise<Language> {
    const language = await this.repo.findOne({ where: { id } });
    if (!language) throw new NotFoundException(`Language #${id} not found`);
    return language;
  }

  async update(id: number, dto: UpdateLanguageDto): Promise<Language> {
    const language = await this.repo.preload({ id, ...dto });
    if (!language) throw new NotFoundException(`Language #${id} not found`);
    return await this.repo.save(language);
  }

  async remove(id: number): Promise<Language> {
    const language = await this.findOne(id);
    return await this.repo.remove(language);
  }
}