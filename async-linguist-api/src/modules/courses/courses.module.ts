import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { CoursesService } from './courses.service';
import { CoursesController } from './courses.controller';
import { Course } from '../../entities/course.entity';
import { Language } from '../../entities/language.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Course, Language])],
  controllers: [CoursesController],
  providers: [CoursesService],
})
export class CoursesModule {}