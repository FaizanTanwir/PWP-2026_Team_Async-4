import { Entity, PrimaryGeneratedColumn, Column, OneToMany } from 'typeorm';
import { Course } from './course.entity';

@Entity('languages')
export class Language {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ unique: true })
  name: string; // e.g., "Finnish"

  @Column({ unique: true, length: 10 })
  code: string; // e.g., "fi"
}