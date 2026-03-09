import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { WordsService } from './words.service';
import { Word } from '../../entities/word.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('WordsService', () => {
  let service: WordsService;
  let repo: Repository<Word>;

  const mockWord = {
    id: 1,
    term: 'Oulu',
    lemma: 'Oulu',
    translation: 'A city',
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        WordsService,
        {
          provide: getRepositoryToken(Word),
          useValue: {
            create: jest.fn().mockImplementation((dto) => dto),
            save: jest.fn().mockResolvedValue(mockWord),
            find: jest.fn().mockResolvedValue([mockWord]),
            findOne: jest.fn().mockResolvedValue(mockWord),
            preload: jest.fn(),
            remove: jest.fn().mockResolvedValue(mockWord),
          },
        },
      ],
    }).compile();

    service = module.get<WordsService>(WordsService);
    repo = module.get<Repository<Word>>(getRepositoryToken(Word));
  });

  describe('update', () => {
    it('should successfully update a word when it exists', async () => {
      const dto = { translation: 'Updated' };
      const updatedWord = { ...mockWord, ...dto };

      // 1. Mock preload to return the merged object
      jest.spyOn(repo, 'preload').mockResolvedValue(updatedWord as any);

      // 2. Mock save to return whatever it receives (the updated object)
      jest.spyOn(repo, 'save').mockResolvedValue(updatedWord as any);

      const result = await service.update(1, dto);

      expect(repo.save).toHaveBeenCalledWith(updatedWord);
      expect(result.translation).toEqual('Updated');
    });

    it('should throw NotFoundException if word to update is missing', async () => {
      jest.spyOn(repo, 'preload').mockResolvedValue(null);
      await expect(service.update(999, { term: 'fail' })).rejects.toThrow(
        NotFoundException,
      );
    });
  });

  describe('remove', () => {
    it('should throw NotFoundException if word to delete is missing', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      await expect(service.remove(999)).rejects.toThrow(NotFoundException);
    });
  });
});
