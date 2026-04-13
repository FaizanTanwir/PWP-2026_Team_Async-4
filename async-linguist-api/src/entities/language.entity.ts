// src/entities/language.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, OneToMany } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger'; // Import the decorator
import { Course } from './course.entity';

@Entity('languages')
export class Language {
  @ApiProperty({ 
    example: 1, 
    description: 'The unique identifier of the language' 
  })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ 
    example: 'Finnish', 
    description: 'The full name of the language' 
  })
  @Column({ unique: true })
  name: string;

  @ApiProperty({ 
    example: 'fi', 
    description: 'The ISO two-letter language code' 
  })
  @Column({ unique: true, length: 10 })
  code: string;

  // We usually don't decorate the OneToMany relations with ApiProperty 
  // unless we specifically want the full array of courses to show up 
  // in every single language response.
  @OneToMany(() => Course, (course) => course.sourceLanguage)
  @ApiProperty({ type: () => [Course], description: 'Courses using this as source' })
  sourceCourses: Course[];

  @OneToMany(() => Course, (course) => course.targetLanguage)
  @ApiProperty({ type: () => [Course], description: 'Courses using this as target' })
  targetCourses: Course[];
}