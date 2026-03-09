import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { AttemptsService } from './attempts.service';
import { Attempt } from '../../entities/attempt.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('AttemptsService', () => {
  let service: AttemptsService;
  let repo: Repository<Attempt>;

  const mockAttempt = {
    id: 1,
    audio_url: 'http://storage.com/audio.wav',
    score: 85,
    sentence: { id: 10 },
    created_at: new Date(),
  };

  const mockRepository = {
    create: jest.fn().mockImplementation((dto) => dto),
    save: jest.fn().mockResolvedValue(mockAttempt),
    find: jest.fn().mockResolvedValue([mockAttempt]),
    findOne: jest.fn().mockResolvedValue(mockAttempt),
    remove: jest.fn().mockResolvedValue(mockAttempt),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AttemptsService,
        {
          provide: getRepositoryToken(Attempt),
          useValue: mockRepository,
        },
      ],
    }).compile();

    service = module.get<AttemptsService>(AttemptsService);
    repo = module.get<Repository<Attempt>>(getRepositoryToken(Attempt));
  });

  describe('create', () => {
    it('should successfully create an attempt linked to a sentence', async () => {
      const dto = { audio_url: 'test.wav', score: 90, sentenceId: 10 };

      await service.create(dto);

      expect(repo.create).toHaveBeenCalledWith({
        audio_url: dto.audio_url,
        score: dto.score,
        sentence: { id: dto.sentenceId },
      });
      expect(repo.save).toHaveBeenCalled();
    });
  });

  describe('findAll', () => {
    it('should return attempts ordered by date with sentence relation', async () => {
      await service.findAll();
      expect(repo.find).toHaveBeenCalledWith({
        relations: ['sentence'],
        order: { created_at: 'DESC' },
      });
    });
  });

  describe('findOne', () => {
    it('should throw NotFoundException if attempt does not exist', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      await expect(service.findOne(999)).rejects.toThrow(NotFoundException);
    });
  });
});
