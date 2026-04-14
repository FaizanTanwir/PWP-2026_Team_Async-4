// src/entities/word.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, OneToMany } from 'typeorm';
import { ApiProperty, ApiHideProperty } from '@nestjs/swagger';
import { SentenceWord } from './sentence-word.entity';

@Entity('words')
export class Word {
  @ApiProperty({ example: 1, description: 'The unique ID of the word' })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'tietokone', description: 'The word in the target language' })
  @Column()
  term: string;

  @ApiProperty({ 
    example: 'tietokone', 
    description: 'The dictionary/root form of the word',
    required: false 
  })
  @Column({ nullable: true })
  lemma: string;

  @ApiProperty({ 
    example: 'computer', 
    description: 'The translation in the source language',
    required: false 
  })
  @Column({ nullable: true })
  translation: string;

  @ApiHideProperty()
  @OneToMany(() => SentenceWord, (sw) => sw.word)
  sentenceWords: SentenceWord[];
}