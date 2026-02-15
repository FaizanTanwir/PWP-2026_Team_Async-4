import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, OneToMany, JoinColumn } from 'typeorm';
import { Language } from './language.entity';
import { Unit } from './unit.entity';

@Entity('courses')
export class Course {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  title: string;

  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'source_language_id' })
  sourceLanguage: Language;

  @ManyToOne(() => Language, { onDelete: 'RESTRICT' })
  @JoinColumn({ name: 'target_language_id' })
  targetLanguage: Language;

  @OneToMany(() => Unit, (unit) => unit.course)
  units: Unit[];
}