import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Course } from '../../entities/course.entity';
import { Language } from '../../entities/language.entity';
import { CreateCourseDto } from './dto/create-course.dto';
import { UpdateCourseDto } from './dto/update-course.dto';

@Injectable()
export class CoursesService {
  constructor(
    @InjectRepository(Course)
    private readonly courseRepo: Repository<Course>,
    @InjectRepository(Language)
    private readonly languageRepo: Repository<Language>,
  ) {}

  async create(createCourseDto: CreateCourseDto): Promise<Course> {
    await this.validateLanguages([
      createCourseDto.sourceLanguageId, 
      createCourseDto.targetLanguageId
    ]);
    
    const course = this.courseRepo.create(createCourseDto);
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

  async update(id: number, updateCourseDto: UpdateCourseDto): Promise<Course> {
    // Collect IDs for bulk validation
    const idsToCheck = [
      updateCourseDto.sourceLanguageId,
      updateCourseDto.targetLanguageId,
    ].filter(id => id !== undefined && id !== null);

    if (idsToCheck.length > 0) {
      await this.validateLanguages(idsToCheck);
    }

    const course = await this.courseRepo.preload({
      id: id,
      ...updateCourseDto,
    });

    if (!course) throw new NotFoundException(`Course #${id} not found`);
    return await this.courseRepo.save(course);
  }

  async remove(id: number): Promise<Course> {
    const course = await this.findOne(id);
    return await this.courseRepo.remove(course);
  }

  // Quick helper for bulk checking Language IDs
  private async validateLanguages(ids: number[]) {
    const uniqueIds = [...new Set(ids)];
    const count = await this.languageRepo.count({
      where: { id: In(uniqueIds) },
    });

    if (count !== uniqueIds.length) {
      throw new BadRequestException('One or more Language IDs are invalid or do not exist.');
    }
  }
}