import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Sentence } from '../../entities/sentence.entity';
import { CreateSentenceDto } from './dto/create-sentence.dto';
import { UpdateSentenceDto } from './dto/update-sentence.dto';

@Injectable()
export class SentencesService {
  constructor(
    @InjectRepository(Sentence)
    private readonly repo: Repository<Sentence>,
  ) {}

  async create(dto: CreateSentenceDto): Promise<Sentence> {
    const { unitId, ...sentenceData } = dto; // Separate unitId from the rest
    const sentence = this.repo.create({
      ...sentenceData,
      unit: { id: unitId } as any,
    });
    return await this.repo.save(sentence);
  }

  findAll(): Promise<Sentence[]> {
    // We include attempts so the frontend can show the high score
    return this.repo.find({ relations: ['unit', 'attempts'] });
  }

  async findOne(id: number): Promise<Sentence> {
    const sentence = await this.repo.findOne({
      where: { id },
      relations: ['unit', 'attempts', 'sentenceWords'],
    });
    if (!sentence) throw new NotFoundException(`Sentence #${id} not found`);
    return sentence;
  }

  async update(id: number, dto: UpdateSentenceDto): Promise<Sentence> {
    const { unitId, ...sentenceData } = dto;
    const sentence = await this.repo.preload({
      id,
      ...sentenceData,
      unit: unitId ? ({ id: unitId } as any) : undefined,
    });

    if (!sentence) {
      throw new NotFoundException(`Sentence #${id} not found`);
    }

    return await this.repo.save(sentence);
  }

  async remove(id: number): Promise<Sentence> {
    const sentence = await this.findOne(id);
    return await this.repo.remove(sentence);
  }
}
