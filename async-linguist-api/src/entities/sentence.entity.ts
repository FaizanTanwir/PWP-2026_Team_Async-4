// src/entities/sentence.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, OneToMany } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Unit } from './unit.entity';
import { Attempt } from './attempt.entity';
import { SentenceWord } from './sentence-word.entity';

@Entity('sentences')
export class Sentence {
  @ApiProperty({ example: 1 })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'Ich lerne Deutsch', description: 'The sentence in the target language' })
  @Column('text')
  text_target: string;

  @ApiProperty({ example: 'I am learning German', description: 'The original source sentence' })
  @Column('text')
  text_source: string;

  @ManyToOne(() => Unit, (unit) => unit.sentences, { onDelete: 'CASCADE' })
  unit: Unit;

  @OneToMany(() => Attempt, (attempt) => attempt.sentence)
  attempts: Attempt[];

  @OneToMany(() => SentenceWord, (sw) => sw.sentence)
  sentenceWords: SentenceWord[];
}