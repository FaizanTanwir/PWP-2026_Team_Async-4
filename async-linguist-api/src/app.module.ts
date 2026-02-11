import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { AppController } from './app.controller';
import { AppService } from './app.service';

// Import your entities so NestJS knows about them
import { Language } from './entities/language.entity';
import { Course } from './entities/course.entity';
import { Unit } from './entities/unit.entity';
import { Sentence } from './entities/sentence.entity';
import { Word } from './entities/word.entity';
import { SentenceWord } from './entities/sentence-word.entity';
import { Attempt } from './entities/attempt.entity';

@Module({
  imports: [
    // 1. Load the .env file globally
    ConfigModule.forRoot({
      isGlobal: true,
    }),

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
          Attempt
        ],
        synchronize: false, 
        logging: true,
      }),
    }),
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}