import {
  Injectable,
  NotFoundException,
  BadRequestException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Not } from 'typeorm';
import { Language } from '../../entities/language.entity';
import { CreateLanguageDto } from './dto/create-language.dto';
import { UpdateLanguageDto } from './dto/update-language.dto';

@Injectable()
export class LanguagesService {
  constructor(
    @InjectRepository(Language) private readonly repo: Repository<Language>,
  ) {}

  async create(dto: CreateLanguageDto): Promise<Language> {
    await this.validateUniqueness(dto);
    return await this.repo.save(this.repo.create(dto));
  }

  async update(id: number, dto: UpdateLanguageDto): Promise<Language> {
    const language = await this.findOne(id);
    await this.validateUniqueness(dto, id);

    Object.assign(language, dto);
    return await this.repo.save(language);
  }

  private async validateUniqueness(dto: UpdateLanguageDto, excludeId?: number) {
    const errors: string[] = [];

    if (dto.name) {
      const nameExists = await this.repo.findOne({
        where: { name: dto.name, id: excludeId ? Not(excludeId) : undefined },
      });
      if (nameExists) errors.push('The name has already been taken.');
    }

    if (dto.code) {
      const codeExists = await this.repo.findOne({
        where: { code: dto.code, id: excludeId ? Not(excludeId) : undefined },
      });
      if (codeExists) errors.push('The code has already been taken.');
    }

    if (errors.length > 0) {
      // Throws a 400 Bad Request with an array of messages
      throw new BadRequestException(errors);
    }
  }

  async findAll() {
    return await this.repo.find({
      relations: ['sourceCourses', 'targetCourses'], // Tell TypeORM to include these
    });
  }

  async findOne(id: number) {
    const language = await this.repo.findOne({
      where: { id },
      relations: ['sourceCourses', 'targetCourses'], // And here too
    });
    if (!language) throw new NotFoundException(`Language #${id} not found`);
    return language;
  }
  async remove(id: number) {
    const language = await this.findOne(id);
    return await this.repo.remove(language);
  }
}
