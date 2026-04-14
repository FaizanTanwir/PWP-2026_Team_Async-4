// src/entities/attempt.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, ManyToOne, CreateDateColumn, JoinColumn } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Sentence } from './sentence.entity';
import { User } from './user.entity';

@Entity('attempts')
export class Attempt {
  @ApiProperty({ example: 1, description: 'The unique ID of the attempt' })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ 
    example: 'https://storage.googleapis.com/audio/sample.mp3', 
    description: 'Cloud storage link to the user recording' 
  })
  @Column()
  audio_url: string;

  @ApiProperty({ example: 92, description: 'Pronunciation accuracy score (0-100)' })
  @Column()
  score: number;

  @ApiProperty({ example: 5, description: 'The ID of the sentence practiced' })
  @Column()
  sentenceId: number;

  @ApiProperty({ example: 1, description: 'The ID of the user who made the attempt' })
  @Column({ name: 'user_id' })
  userId: number;

  @ManyToOne(() => User, (user) => user.attempts, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'user_id' })
  user: User;

  @ApiProperty({ example: '2026-04-13T12:00:00Z', description: 'When the attempt was submitted' })
  @CreateDateColumn({ type: 'timestamp' })
  created_at: Date;

  @ManyToOne(() => Sentence)
  @ApiProperty({ type: () => Sentence, description: 'The associated sentence details' })
  sentence: Sentence;
}