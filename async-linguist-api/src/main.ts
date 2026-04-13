import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { ValidationPipe } from '@nestjs/common';
import { TypeOrmExceptionFilter } from './common/filters/typeorm-exception.filter';
import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  const config = new DocumentBuilder()
    .setTitle('Linguist API')
    .setDescription('The REST API for the Language Learning Platform')
    .setVersion('1.0')
    .addBearerAuth()
    .build();

  const document = SwaggerModule.createDocument(app, config);
  SwaggerModule.setup('api/docs', app, document);

  // This handles empty or invalid requests gracefully
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true, // Removes fields not in the DTO
      forbidNonWhitelisted: true, // Errors if extra fields are sent
      transform: true, // Converts types automatically
    }),
  );

  app.useGlobalFilters(new TypeOrmExceptionFilter());

  await app.listen(process.env.PORT ?? 3000);
}
bootstrap();
