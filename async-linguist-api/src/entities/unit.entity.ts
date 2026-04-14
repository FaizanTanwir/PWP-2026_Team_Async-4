// src/entities/unit.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, OneToMany } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Course } from './course.entity';
import { Sentence } from './sentence.entity';

@Entity('units')
export class Unit {
  @ApiProperty({ example: 1, description: 'The unique ID of the unit' })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'Basic Greetings', description: 'The title of the learning unit' })
  @Column()
  title: string;

  @ManyToOne(() => Course, (course) => course.units, { onDelete: 'CASCADE' })
  course: Course;

  @ApiProperty({ type: () => [Sentence], description: 'List of sentences belonging to this unit' })
  @OneToMany(() => Sentence, (sentence) => sentence.unit)
  sentences: Sentence[];
}