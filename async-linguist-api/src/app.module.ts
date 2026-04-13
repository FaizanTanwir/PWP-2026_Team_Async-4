import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { AppController } from './app.controller';
import { AppService } from './app.service';

import { CacheModule } from '@nestjs/cache-manager';

// Import your entities so NestJS knows about them
import { Language } from './entities/language.entity';
import { Course } from './entities/course.entity';
import { Unit } from './entities/unit.entity';
import { Sentence } from './entities/sentence.entity';
import { Word } from './entities/word.entity';
import { SentenceWord } from './entities/sentence-word.entity';
import { Attempt } from './entities/attempt.entity';
import { User } from './entities/user.entity';

import { LanguagesModule } from './modules/languages/languages.module';
import { CoursesModule } from './modules/courses/courses.module';
import { AttemptsModule } from './modules/attempts/attempts.module';
import { UnitsModule } from './modules/units/units.module';
import { SentencesModule } from './modules/sentences/sentences.module';
import { WordsModule } from './modules/words/words.module';
import { UsersModule } from './users/users.module';
import { AuthModule } from './auth/auth.module';

@Module({
  imports: [
    // 1. Load the .env file globally
    ConfigModule.forRoot({
      isGlobal: true,
    }),
    CacheModule.register({ isGlobal: true }),

    // 2. Configure TypeORM asynchronously to use ConfigService
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        type: 'postgres',
        host: configService.get<string>('DB_HOST'),
        port: configService.get<number>('DB_PORT'),
        username: configService.get<string>('DB_USERNAME'),
        password: configService.get<string>('DB_PASSWORD'),
        database: configService.get<string>('DB_NAME'),
        entities: [
          Language,
          Course,
          Unit,
          Sentence,
          Word,
          SentenceWord,
          Attempt,
          User,
        ],
        synchronize: false,
        logging: true,
      }),
    }),

    LanguagesModule,
    CoursesModule,
    AttemptsModule,
    UnitsModule,
    SentencesModule,
    WordsModule,
    UsersModule,
    AuthModule,
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
