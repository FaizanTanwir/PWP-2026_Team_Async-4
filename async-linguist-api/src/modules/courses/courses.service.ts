import { Injectable, NotFoundException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Course } from '../../entities/course.entity';
import { CreateCourseDto } from './dto/create-course.dto';
import { UpdateCourseDto } from './dto/update-course.dto';

@Injectable()
export class CoursesService {
  constructor(
    @InjectRepository(Course)
    private readonly courseRepo: Repository<Course>,
  ) {}

  async create(dto: CreateCourseDto): Promise<Course> {
    const course = this.courseRepo.create(dto);
    return await this.courseRepo.save(course);
  }

  findAll(): Promise<Course[]> {
    return this.courseRepo.find({ 
      relations: ['sourceLanguage', 'targetLanguage', 'units'] 
    });
  }

  async findOne(id: number): Promise<Course> {
    const course = await this.courseRepo.findOne({
      where: { id },
      relations: ['sourceLanguage', 'targetLanguage', 'units'],
    });
    if (!course) throw new NotFoundException(`Course with ID ${id} not found`);
    return course;
  }

  async update(id: number, dto: UpdateCourseDto): Promise<Course> {
    const course = await this.courseRepo.preload({ id, ...dto });
    if (!course) throw new NotFoundException(`Course #${id} not found`);
    return await this.courseRepo.save(course);
  }

  async remove(id: number): Promise<Course> {
    const course = await this.findOne(id);
    return await this.courseRepo.remove(course);
  }
}