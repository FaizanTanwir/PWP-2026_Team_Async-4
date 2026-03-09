import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Word } from '../../entities/word.entity';
import { CreateWordDto } from './dto/create-word.dto';
import { UpdateWordDto } from './dto/update-word.dto';

@Injectable()
export class WordsService {
  constructor(
    @InjectRepository(Word)
    private readonly repo: Repository<Word>,
  ) {}

  async create(dto: CreateWordDto): Promise<Word> {
    const word = this.repo.create(dto);
    return await this.repo.save(word);
  }

  findAll(): Promise<Word[]> {
    return this.repo.find();
  }

  async findOne(id: number): Promise<Word> {
    const word = await this.repo.findOne({ where: { id } });
    if (!word) throw new NotFoundException(`Word #${id} not found`);
    return word;
  }

  async update(id: number, dto: UpdateWordDto): Promise<Word> {
    const word = await this.repo.preload({ id, ...dto });
    if (!word) throw new NotFoundException(`Word #${id} not found`);
    return await this.repo.save(word);
  }

  async remove(id: number): Promise<Word> {
    const word = await this.findOne(id);
    return await this.repo.remove(word);
  }
}