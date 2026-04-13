// src/entities/attempt.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, CreateDateColumn } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Sentence } from './sentence.entity';

@Entity('attempts')
export class Attempt {
  @ApiProperty({ example: 1 })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'https://storage.googleapis.com/audio/sample.mp3' })
  @Column()
  audio_url: string;

  @ApiProperty({ example: 92 })
  @Column()
  score: number;

  @ApiProperty({ example: 5 })
  @Column()
  sentenceId: number;

  @ApiProperty({ example: '2026-04-13T12:00:00Z', description: 'Timestamp of the attempt' })
  @CreateDateColumn({ type: 'timestamp' })
  created_at: Date;

  @ManyToOne(() => Sentence)
  sentence: Sentence;
}