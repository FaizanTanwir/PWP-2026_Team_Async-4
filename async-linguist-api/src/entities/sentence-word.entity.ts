import { Entity, PrimaryColumn, ManyToOne, JoinColumn } from 'typeorm';
import { Sentence } from './sentence.entity';
import { Word } from './word.entity';

@Entity('sentence_words')
export class SentenceWord {
  @PrimaryColumn()
  sentence_id: number;

  @PrimaryColumn()
  word_id: number;

  @ManyToOne(() => Sentence, (s) => s.sentenceWords, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'sentence_id' })
  sentence: Sentence;

  @ManyToOne(() => Word, (w) => w.sentenceWords, { onDelete: 'CASCADE' })
  @JoinColumn({ name: 'word_id' })
  word: Word;
}
