import { AppDataSource } from './data-source';
import { Language } from '../entities/language.entity';
import { Course } from '../entities/course.entity';
import { Unit } from '../entities/unit.entity';
import { Sentence } from '../entities/sentence.entity';
import { Word } from '../entities/word.entity';
import { SentenceWord } from '../entities/sentence-word.entity';
import { User, UserRole } from '../entities/user.entity';
import * as bcrypt from 'bcrypt';

async function seed() {
  console.log('🌱 Starting comprehensive database seeding...');
  await AppDataSource.initialize();

  const queryRunner = AppDataSource.createQueryRunner();
  console.log('Clearing old data...');
  await queryRunner.query(
    'TRUNCATE TABLE "languages", "courses", "units", "sentences", "words", "sentence_words", "attempts", "users" RESTART IDENTITY CASCADE;',
  );

  const langRepo = AppDataSource.getRepository(Language);
  const courseRepo = AppDataSource.getRepository(Course);
  const unitRepo = AppDataSource.getRepository(Unit);
  const sentenceRepo = AppDataSource.getRepository(Sentence);
  const wordRepo = AppDataSource.getRepository(Word);
  const sentenceWordRepo = AppDataSource.getRepository(SentenceWord);
  const userRepo = AppDataSource.getRepository(User); // 3. Get User Repo

  // --- 4. Seed Users ---
  console.log('Creating users...');
  const salt = await bcrypt.genSalt();
  const hashedPassword = await bcrypt.hash('password', salt);

  const users = await userRepo.save([
    {
      email: 'admin@linguist.com',
      password: hashedPassword,
      role: UserRole.ADMIN,
    },
    {
      email: 'teacher@linguist.com',
      password: hashedPassword,
      role: UserRole.TEACHER,
    },
    {
      email: 'student@linguist.com',
      password: hashedPassword,
      role: UserRole.STUDENT,
    },
  ]);

  // Pick the teacher user to be the owner of the course
  const teacher = users[1]; 

  // --- Keep existing linguistic data seeding ---
  // 1. Seed Languages
  const en = await langRepo.save({ name: 'English', code: 'en' });
  const fi = await langRepo.save({ name: 'Finnish', code: 'fi' });

  // 2. Seed Course
  const course = await courseRepo.save({
    title: 'Finnish for Beginners',
    sourceLanguage: en,
    targetLanguage: fi,
    createdById: teacher.id,
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

      await sentenceWordRepo.save({
        sentence_id: sentence.id,
        word_id: word.id,
      });
    }
  }

  console.log('✅ Seeding complete! Admin and Student users created.');
  await AppDataSource.destroy();
}

seed().catch((err) => {
  console.error('❌ Error during seeding:', err);
  process.exit(1);
});