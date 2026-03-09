import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { SentencesService } from './sentences.service';
import { Sentence } from '../../entities/sentence.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('SentencesService', () => {
  let service: SentencesService;
  let repo: Repository<Sentence>;

  const mockSentence = {
    id: 1,
    text_target: 'Minä rakastan Oulua',
    text_source: 'I love Oulu',
    unit: { id: 5 },
    attempts: [],
  };

  const mockRepository = {
    create: jest.fn().mockImplementation((dto) => dto),
    save: jest.fn(),
    find: jest.fn(),
    findOne: jest.fn(),
    preload: jest.fn(),
    remove: jest.fn(),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        SentencesService,
        {
          provide: getRepositoryToken(Sentence),
          useValue: mockRepository,
        },
      ],
    }).compile();

    service = module.get<SentencesService>(SentencesService);
    repo = module.get<Repository<Sentence>>(getRepositoryToken(Sentence));
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('create', () => {
    it('should create a sentence and link it to a unit', async () => {
      const dto = { text_target: 'Hei', text_source: 'Hi', unitId: 5 };
      jest.spyOn(repo, 'save').mockResolvedValue({ id: 1, ...dto } as any);

      const result = await service.create(dto);

      expect(repo.create).toHaveBeenCalledWith({
        text_target: 'Hei',
        text_source: 'Hi',
        unit: { id: 5 },
      });
      expect(result.id).toEqual(1);
    });
  });

  describe('findOne', () => {
    it('should return a sentence with all relations for the detail view', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockSentence as any);

      const result = await service.findOne(1);

      expect(repo.findOne).toHaveBeenCalledWith({
        where: { id: 1 },
        relations: ['unit', 'attempts', 'sentenceWords'],
      });
      expect(result).toEqual(mockSentence);
    });

    it('should throw NotFoundException if sentence is missing', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      await expect(service.findOne(999)).rejects.toThrow(NotFoundException);
    });
  });

  describe('update (The Critical Edge Cases)', () => {
    it('should partially update text without changing the unit', async () => {
      const updateDto = { text_target: 'Uusi teksti' };
      jest.spyOn(repo, 'preload').mockResolvedValue({ ...mockSentence, ...updateDto } as any);
      jest.spyOn(repo, 'save').mockResolvedValue({ ...mockSentence, ...updateDto } as any);

      await service.update(1, updateDto);

      expect(repo.preload).toHaveBeenCalledWith({
        id: 1,
        text_target: 'Uusi teksti',
        unit: undefined, // Crucial: should not overwrite unit if unitId is missing from DTO
      });
    });

    it('should update the unit relationship if unitId is provided', async () => {
      const updateDto = { unitId: 10 };
      jest.spyOn(repo, 'preload').mockResolvedValue({ ...mockSentence, unit: { id: 10 } } as any);
      jest.spyOn(repo, 'save').mockResolvedValue({ ...mockSentence, unit: { id: 10 } } as any);

      await service.update(1, updateDto);

      expect(repo.preload).toHaveBeenCalledWith({
        id: 1,
        unit: { id: 10 },
      });
    });

    it('should throw NotFoundException if preload fails (sentence ID not in DB)', async () => {
      jest.spyOn(repo, 'preload').mockResolvedValue(null);
      await expect(service.update(999, { text_target: 'Fail' })).rejects.toThrow(NotFoundException);
    });
  });

  describe('remove', () => {
    it('should find and then delete the sentence', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockSentence as any);
      jest.spyOn(repo, 'remove').mockResolvedValue(mockSentence as any);

      await service.remove(1);

      expect(repo.remove).toHaveBeenCalledWith(mockSentence);
    });
  });
});