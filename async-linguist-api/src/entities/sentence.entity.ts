import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  ManyToOne,
  OneToMany,
} from 'typeorm';
import { Unit } from './unit.entity';
import { Attempt } from './attempt.entity';
import { SentenceWord } from './sentence-word.entity';

@Entity('sentences')
export class Sentence {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('text')
  text_target: string; // Finnish

  @Column('text')
  text_source: string; // English

  @ManyToOne(() => Unit, (unit) => unit.sentences, { onDelete: 'CASCADE' })
  unit: Unit;

  @OneToMany(() => Attempt, (attempt) => attempt.sentence)
  attempts: Attempt[];

  @OneToMany(() => SentenceWord, (sw) => sw.sentence)
  sentenceWords: SentenceWord[];
}
