import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  ManyToOne,
  OneToMany,
  JoinColumn,
} from 'typeorm';
import { Language } from './language.entity';
import { Unit } from './unit.entity'; // Ensure this import is here

@Entity('courses')
export class Course {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  title: string;

  @Column({ name: 'source_language_id' })
  sourceLanguageId: number;

  @Column({ name: 'target_language_id' })
  targetLanguageId: number;

  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'source_language_id' })
  sourceLanguage: Language;

  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'target_language_id' })
  targetLanguage: Language;

  // THIS IS THE MISSING PIECE:
  @OneToMany(() => Unit, (unit) => unit.course)
  units: Unit[];
}
