import { Entity, PrimaryGeneratedColumn, Column, OneToMany } from 'typeorm';

import { SentenceWord } from './sentence-word.entity';

@Entity('words')
export class Word {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  term: string;

  @Column({ nullable: true })
  lemma: string; // Dictionary form

  @Column({ nullable: true })
  translation: string;

  @OneToMany(() => SentenceWord, (sw) => sw.word)
  sentenceWords: SentenceWord[];
}
