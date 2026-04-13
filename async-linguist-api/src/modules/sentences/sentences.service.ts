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
    const { text_target, unitId, text_source } = dto;

    // 1. Fetch the Unit and its related Course/Languages
    const unit = await this.unitRepo.findOne({
      where: { id: unitId },
      relations: ['course', 'course.sourceLanguage', 'course.targetLanguage'],
    });

    if (!unit) {
      throw new NotFoundException(`Unit with ID ${unitId} not found`);
    }

    // Dynamic Language Codes from DB (e.g., 'fi', 'en', 'ur')
    const targetLangCode = unit.course.targetLanguage.code;
    const sourceLangCode = unit.course.sourceLanguage.code;

    console.warn(`Creating sentence for Unit ${unitId} with target language "${targetLangCode}" and source language "${sourceLangCode}"`);

    // 2. Translation Logic using Dynamic Codes
    let finalTarget = text_target;
    if (!finalTarget) {
      finalTarget = await this.translate(text_source, sourceLangCode, targetLangCode);
    }

    // let finalSource = text_source;
    // if (!finalSource) {
    //   finalSource = await this.translate(finalTarget, targetLangCode, sourceLangCode);
    // }

    // 3. Save the Sentence
    const sentence = this.repo.create({
      text_target: finalTarget,
      text_source,
      unit: unit,
    });
    const savedSentence = await this.repo.save(sentence);

    // 4. Word Breakdown using Dynamic Codes
    const words = finalTarget.split(/\s+/).filter((w) => w.length > 0);

    await Promise.all(
      words.map(async (term) => {
        // Correct Direction: from TARGET (German) to SOURCE (English)
        const translation = await this.translate(term, targetLangCode, sourceLangCode);

        let wordEntity = await this.wordRepo.findOneBy({ term });
        if (!wordEntity) {
          wordEntity = await this.wordRepo.save(
            this.wordRepo.create({ 
                term, 
                translation,
                // Pro-tip: If your Word entity has a languageId, set it here!
            })
          );
        }

        return this.sentenceWordRepo.save({
          sentence_id: savedSentence.id,
          word_id: wordEntity.id,
        });
      }),
    );

    return savedSentence;
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
