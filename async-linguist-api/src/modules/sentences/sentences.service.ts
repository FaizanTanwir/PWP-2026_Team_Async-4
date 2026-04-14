import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Sentence } from '../../entities/sentence.entity';
import { Word } from '../../entities/word.entity';
import { SentenceWord } from '../../entities/sentence-word.entity';
import { CreateSentenceDto } from './dto/create-sentence.dto';
import { UpdateSentenceDto } from './dto/update-sentence.dto';
import axios from 'axios';
import { ConfigService } from '@nestjs/config';
import { Unit } from 'src/entities/unit.entity';

@Injectable()
export class SentencesService {
  constructor(
    @InjectRepository(Sentence)
    private readonly repo: Repository<Sentence>,

    @InjectRepository(Word)
    private readonly wordRepo: Repository<Word>,

    @InjectRepository(Unit) private readonly unitRepo: Repository<Unit>,

    @InjectRepository(SentenceWord)
    private readonly sentenceWordRepo: Repository<SentenceWord>,

    private readonly configService: ConfigService,
  ) {}

  async create(dto: CreateSentenceDto): Promise<Sentence> {
  return await this.repo.manager.transaction(async (transactionalEntityManager) => {
    const { text_target, unitId, text_source } = dto;

    const unit = await transactionalEntityManager.findOne(Unit, {
      where: { id: unitId },
      relations: ['course', 'course.sourceLanguage', 'course.targetLanguage'],
    });

    if (!unit) throw new NotFoundException(`Unit with ID ${unitId} not found`);

    const targetLangCode = unit.course.targetLanguage.code;
    const sourceLangCode = unit.course.sourceLanguage.code;

    let finalTarget = text_target;
    if (!finalTarget) {
      finalTarget = await this.translate(text_source, sourceLangCode, targetLangCode);
    }

    // Save sentence via transactional manager
    const sentence = transactionalEntityManager.create(Sentence, {
      text_target: finalTarget,
      text_source,
      unit: unit,
    });
    const savedSentence = await transactionalEntityManager.save(sentence);

    // Regex to remove punctuation like . , ! ?
    const words = finalTarget
      .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, "")
      .split(/\s+/)
      .filter((w) => w.length > 0);

    await Promise.all(
      words.map(async (term) => {
        const translation = await this.translate(term, targetLangCode, sourceLangCode);

        // Check for existing word within the transaction
        let wordEntity = await transactionalEntityManager.findOneBy(Word, { term });
        if (!wordEntity) {
          wordEntity = await transactionalEntityManager.save(
            transactionalEntityManager.create(Word, { term, translation })
          );
        }

        await transactionalEntityManager.save(SentenceWord, {
          sentence: savedSentence, // TypeORM handles IDs if you pass the object
          word: wordEntity,
        });
      }),
    );

    return savedSentence;
  });
}

  private async translate(text: string, from: string, to: string): Promise<string> {
    try {
      const url = this.configService.get<string>('TRANSLATOR_URL') || 'http://localhost:5000/translate';

      console.log(`Translating "${text}" from ${from} to ${to} using ${url}`);

      const response = await axios.post(url, {
        q: text,
        source: from,
        target: to,
        format: 'text',
        api_key: '',
      });

      return response.data.translatedText;
    } catch (error) {
      console.error(`Translation failed for: "${text}"`, error.message);
      // Fallback: return the original text so the DB record can still be created
      return text; 
    }
  }

  findAll(): Promise<Sentence[]> {
    // We include attempts so the frontend can show the high score
    return this.repo.find({ relations: ['unit', 'attempts'] });
  }

  async findOne(id: number): Promise<Sentence> {
    const sentence = await this.repo.findOne({
      where: { id },
      relations: ['unit', 'attempts', 'sentenceWords'],
    });
    if (!sentence) throw new NotFoundException(`Sentence #${id} not found`);
    return sentence;
  }

  async update(id: number, dto: UpdateSentenceDto): Promise<Sentence> {
    const { unitId, ...sentenceData } = dto;
    const sentence = await this.repo.preload({
      id,
      ...sentenceData,
      unit: unitId ? ({ id: unitId } as any) : undefined,
    });

    if (!sentence) {
      throw new NotFoundException(`Sentence #${id} not found`);
    }

    return await this.repo.save(sentence);
  }

  async remove(id: number): Promise<Sentence> {
    const sentence = await this.findOne(id);
    return await this.repo.remove(sentence);
  }
}
