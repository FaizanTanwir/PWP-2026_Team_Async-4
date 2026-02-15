import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, CreateDateColumn } from 'typeorm';
import { Sentence } from './sentence.entity';

@Entity('attempts')
export class Attempt {
  @PrimaryGeneratedColumn()
  id: number;

  @Column('text')
  audio_url: string;

  @Column('float', { default: 0 })
  score: number;

  @CreateDateColumn()
  created_at: Date;

  @ManyToOne(() => Sentence, (s) => s.attempts, { onDelete: 'CASCADE' })
  sentence: Sentence;
}