import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { SentencesService } from './sentences.service';
import { SentencesController } from './sentences.controller';
import { Sentence } from '../../entities/sentence.entity';
import { Word } from '../../entities/word.entity';
import { SentenceWord } from '../../entities/sentence-word.entity';
import { Unit } from '../../entities/unit.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Sentence, 
      Word, 
      SentenceWord,
      Unit
    ]),
  ],
  controllers: [SentencesController],
  providers: [SentencesService],
})
export class SentencesModule {}
