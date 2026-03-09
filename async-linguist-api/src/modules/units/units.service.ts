import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Unit } from '../../entities/unit.entity';
import { CreateUnitDto } from './dto/create-unit.dto';
import { UpdateUnitDto } from './dto/update-unit.dto';

@Injectable()
export class UnitsService {
  constructor(
    @InjectRepository(Unit)
    private readonly repo: Repository<Unit>,
  ) {}

  async create(dto: CreateUnitDto): Promise<Unit> {
    const unit = this.repo.create({
      title: dto.title,
      course: { id: dto.courseId } as any,
    });
    return await this.repo.save(unit);
  }

  findAll(): Promise<Unit[]> {
    return this.repo.find({ relations: ['course', 'sentences'] });
  }

  async findOne(id: number): Promise<Unit> {
    const unit = await this.repo.findOne({
      where: { id },
      relations: ['course', 'sentences'],
    });
    if (!unit) throw new NotFoundException(`Unit #${id} not found`);
    return unit;
  }

  async update(id: number, dto: UpdateUnitDto): Promise<Unit> {
    const unit = await this.repo.preload({
      id,
      title: dto.title,
      course: dto.courseId ? ({ id: dto.courseId } as any) : undefined,
    });
    if (!unit) throw new NotFoundException(`Unit #${id} not found`);
    return await this.repo.save(unit);
  }

  async remove(id: number): Promise<Unit> {
    const unit = await this.findOne(id);
    return await this.repo.remove(unit);
  }
}