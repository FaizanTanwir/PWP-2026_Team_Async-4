import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { AttemptsService } from './attempts.service';
import { AttemptsController } from './attempts.controller';
import { Attempt } from '../../entities/attempt.entity';

@Module({
  // 1. This tells TypeORM to create a Repository for the Attempt entity
  imports: [TypeOrmModule.forFeature([Attempt])],
  
  // 2. This registers the Controller to handle incoming HTTP requests
  controllers: [AttemptsController],
  
  // 3. This makes the Service available for Injection
  providers: [AttemptsService],
  
  // 4. Export it if other modules (like Reports or User Stats) need it later
  exports: [AttemptsService],
})
export class AttemptsModule {}