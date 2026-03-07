import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { ValidationPipe } from '@nestjs/common';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  // This handles empty or invalid requests gracefully
  app.useGlobalPipes(new ValidationPipe({
    whitelist: true,               // Removes fields not in the DTO
    forbidNonWhitelisted: true,    // Errors if extra fields are sent
    transform: true,               // Converts types automatically
  }));

  await app.listen(process.env.PORT ?? 3000);
}
bootstrap();
