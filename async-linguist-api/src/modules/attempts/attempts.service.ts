import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Attempt } from '../../entities/attempt.entity';
import { CreateAttemptDto } from './dto/create-attempt.dto';

@Injectable()
export class AttemptsService {
  constructor(
    @InjectRepository(Attempt)
    private readonly repo: Repository<Attempt>,
  ) {}

  async create(dto: CreateAttemptDto): Promise<Attempt> {
    // We create the entity and map the sentenceId to the sentence relation
    const attempt = this.repo.create({
      audio_url: dto.audio_url,
      score: dto.score,
      sentence: { id: dto.sentenceId } as any, // Link by ID without extra queries
    });
    
    return await this.repo.save(attempt);
  }

  findAll(): Promise<Attempt[]> {
    return this.repo.find({ 
      relations: ['sentence'],
      order: { created_at: 'DESC' } // Newest attempts first
    });
  }

  async findOne(id: number): Promise<Attempt> {
    const attempt = await this.repo.findOne({
      where: { id },
      relations: ['sentence'],
    });
    if (!attempt) throw new NotFoundException(`Attempt #${id} not found`);
    return attempt;
  }

  async remove(id: number): Promise<Attempt> {
    const attempt = await this.findOne(id);
    return await this.repo.remove(attempt);
  }
}