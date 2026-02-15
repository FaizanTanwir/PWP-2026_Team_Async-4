import { AppDataSource } from './data-source';
import { Language } from '../entities/language.entity';
import { Course } from '../entities/course.entity';
import { Unit } from '../entities/unit.entity';
import { Sentence } from '../entities/sentence.entity';
import { Word } from '../entities/word.entity';
import { SentenceWord } from '../entities/sentence-word.entity';

async function seed() {
  console.log('🌱 Starting comprehensive database seeding...');
  await AppDataSource.initialize();

  // Get the query runner to execute raw SQL for a quick clean
  const queryRunner = AppDataSource.createQueryRunner();
  console.log('Clearing old data...');
  await queryRunner.query('TRUNCATE TABLE "languages", "courses", "units", "sentences", "words", "sentence_words", "attempts" RESTART IDENTITY CASCADE;');
  

  const langRepo = AppDataSource.getRepository(Language);
  const courseRepo = AppDataSource.getRepository(Course);
  const unitRepo = AppDataSource.getRepository(Unit);
  const sentenceRepo = AppDataSource.getRepository(Sentence);
  const wordRepo = AppDataSource.getRepository(Word);
  const sentenceWordRepo = AppDataSource.getRepository(SentenceWord);

  // 1. Seed Languages
  const en = await langRepo.save({ name: 'English', code: 'en' });
  const fi = await langRepo.save({ name: 'Finnish', code: 'fi' });

  // 2. Seed Course
  const course = await courseRepo.save({
    title: 'Finnish for Beginners',
    sourceLanguage: en,
    targetLanguage: fi,
  });

  // 3. Seed Unit
  const unit1 = await unitRepo.save({
    title: 'Greetings & Basics',
    course: course,
  });

  // 4. Seed Sentences & Words
  const data = [
    { target: 'Hyvää huomenta', source: 'Good morning', words: ['Hyvää', 'huomenta'] },
    { target: 'Mitä kuuluu?', source: 'How are you?', words: ['Mitä', 'kuuluu'] },
    { target: 'Kiitos paljon', source: 'Thank you very much', words: ['Kiitos', 'paljon'] },
    { target: 'Hauska tavata', source: 'Nice to meet you', words: ['Hauska', 'tavata'] },
  ];

  for (const item of data) {
    const sentence = await sentenceRepo.save({
      text_target: item.target,
      text_source: item.source,
      unit: unit1,
    });

    for (const term of item.words) {
      let word = await wordRepo.findOne({ where: { term } });
      if (!word) {
        word = await wordRepo.save({ term });
      }
      
      // Link sentence to words in the Join Table
      await sentenceWordRepo.save({
        sentence_id: sentence.id,
        word_id: word.id
      });
    }
  }

  console.log('✅ Seeding complete! Check pgAdmin for your data.');
  await AppDataSource.destroy();
}

seed().catch((err) => {
  console.error('❌ Error during seeding:', err);
  process.exit(1);
});