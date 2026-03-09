import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  ManyToOne,
  OneToMany,
} from 'typeorm';
import { Course } from './course.entity';
import { Sentence } from './sentence.entity';

@Entity('units')
export class Unit {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  title: string;

  @ManyToOne(() => Course, (course) => course.units, { onDelete: 'CASCADE' })
  course: Course;

  @OneToMany(() => Sentence, (sentence) => sentence.unit)
  sentences: Sentence[];
}
