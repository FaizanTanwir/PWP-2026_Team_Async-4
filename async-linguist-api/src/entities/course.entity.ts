// src/entities/course.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, OneToMany, JoinColumn } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Language } from './language.entity';
import { Unit } from './unit.entity';

@Entity('courses')
export class Course {
  @ApiProperty({ example: 1, description: 'The unique ID of the course' })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'Finnish for Beginners', description: 'The title' })
  @Column()
  title: string;

  @ApiProperty({ example: 1, description: 'Source language ID' })
  @Column({ name: 'source_language_id' })
  sourceLanguageId: number;

  @ApiProperty({ example: 2, description: 'Target language ID' })
  @Column({ name: 'target_language_id' })
  targetLanguageId: number;

  @ApiProperty({ type: () => Language, description: 'The source language details' })
  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'source_language_id' })
  sourceLanguage: Language;

  @ApiProperty({ type: () => Language, description: 'The target language details' })
  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'target_language_id' })
  targetLanguage: Language;

  @ApiProperty({ type: () => [Unit], description: 'List of units in this course' })
  @OneToMany(() => Unit, (unit) => unit.course)
  units: Unit[];
}