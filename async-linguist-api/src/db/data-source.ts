import { DataSource } from 'typeorm';
import * as dotenv from 'dotenv'; // 1. Import dotenv
import { Language } from '../entities/language.entity';
import { Course } from '../entities/course.entity';
import { Unit } from '../entities/unit.entity';
import { Sentence } from '../entities/sentence.entity';
import { Word } from '../entities/word.entity';
import { SentenceWord } from '../entities/sentence-word.entity';
import { Attempt } from '../entities/attempt.entity';

// 2. Load the .env file from the root directory
dotenv.config();

export const AppDataSource = new DataSource({
  type: 'postgres',
  host: process.env.DB_HOST,
  port: parseInt(process.env.DB_PORT || '5432', 10),
  username: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  synchronize: false,
  logging: true,
  entities: [Language, Course, Unit, Sentence, Word, SentenceWord, Attempt],
  migrations: ['src/migrations/*.ts'],
});
