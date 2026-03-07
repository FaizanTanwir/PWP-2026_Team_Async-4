import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { LanguagesService } from './languages.service';
import { Language } from '../../entities/language.entity';
import { Repository, Not } from 'typeorm';
import { BadRequestException, NotFoundException } from '@nestjs/common';

describe('LanguagesService', () => {
  let service: LanguagesService;
  let repo: Repository<Language>;

  const mockLanguage = { id: 1, name: 'Finnish', code: 'fi' };

  const mockRepository = {
    create: jest.fn().mockImplementation((dto) => dto),
    save: jest.fn(),
    find: jest.fn(),
    findOne: jest.fn(),
    findOneBy: jest.fn(),
    remove: jest.fn(),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        LanguagesService,
        {
          provide: getRepositoryToken(Language),
          useValue: mockRepository,
        },
      ],
    }).compile();

    service = module.get<LanguagesService>(LanguagesService);
    repo = module.get<Repository<Language>>(getRepositoryToken(Language));
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  // --- FIND ALL SCENARIOS ---
  describe('findAll', () => {
    it('should return an array of languages when data exists', async () => {
      // Testing: Normal find all returns data
      jest.spyOn(repo, 'find').mockResolvedValue([mockLanguage] as any);
      const result = await service.findAll();
      expect(result).toEqual([mockLanguage]);
    });

    it('should return an empty array if no languages exist', async () => {
      // Testing: find all returns empty array if DB is empty
      jest.spyOn(repo, 'find').mockResolvedValue([]);
      const result = await service.findAll();
      expect(result).toEqual([]);
    });
  });

  // --- CREATE SCENARIOS ---
  describe('create', () => {
    it('should successfully create a language (Normal Create)', async () => {
      // Testing: Successful path without duplicates
      const dto = { name: 'English', code: 'en' };
      jest.spyOn(repo, 'findOne').mockResolvedValue(null); 
      jest.spyOn(repo, 'save').mockResolvedValue({ id: 2, ...dto } as any);

      const result = await service.create(dto);
      expect(result).toEqual({ id: 2, ...dto });
    });

    it('should throw BadRequestException if name/code already exist (Duplicate Check)', async () => {
      // Testing: Creating a duplicate throws the correct array of errors
      const dto = { name: 'Finnish', code: 'fi' };
      jest.spyOn(repo, 'findOne')
        .mockResolvedValueOnce(mockLanguage as any) // Name check fails
        .mockResolvedValueOnce(mockLanguage as any); // Code check fails

      try {
        await service.create(dto);
      } catch (e) {
        expect(e).toBeInstanceOf(BadRequestException);
        const response: any = e.getResponse();
        expect(response.message).toContain('The name has already been taken.');
        expect(response.message).toContain('The code has already been taken.');
      }
    });
  });

  // --- UPDATE SCENARIOS ---
  describe('update', () => {
    it('should successfully update an existing language (Normal Update)', async () => {
      // Testing: Happy path update
      const updateDto = { name: 'Suomi' };
      jest.spyOn(repo, 'findOneBy').mockResolvedValue(mockLanguage as any);
      jest.spyOn(repo, 'findOne').mockResolvedValue(null); 
      jest.spyOn(repo, 'save').mockResolvedValue({ ...mockLanguage, ...updateDto } as any);

      const result = await service.update(1, updateDto);
      expect(result.name).toEqual('Suomi');
    });

    it('should throw NotFoundException if language doesn’t exist (Update non-existent)', async () => {
      // Testing: Guard check for existence before update
      jest.spyOn(repo, 'findOneBy').mockResolvedValue(null);
      await expect(service.update(99, { name: 'New' })).rejects.toThrow(NotFoundException);
    });

    it('should throw BadRequestException if updating to a duplicate name/code', async () => {
      // Testing: Update fails if target name/code is taken by ANOTHER record
      const updateDto = { name: 'English', code: 'en' };
      jest.spyOn(repo, 'findOneBy').mockResolvedValue(mockLanguage as any);
      jest.spyOn(repo, 'findOne')
        .mockResolvedValueOnce({ id: 2, name: 'English' } as any) // Name taken by ID 2
        .mockResolvedValueOnce({ id: 3, code: 'en' } as any);    // Code taken by ID 3

      try {
        await service.update(1, updateDto);
      } catch (e) {
        expect(e).toBeInstanceOf(BadRequestException);
        const response: any = e.getResponse();
        expect(response.message).toContain('The name has already been taken.');
        expect(response.message).toContain('The code has already been taken.');
      }
    });
  });

  // --- REMOVE SCENARIOS ---
  describe('remove', () => {
    it('should successfully remove a language (Normal Remove)', async () => {
      jest.spyOn(repo, 'findOneBy').mockResolvedValue(mockLanguage as any);
      jest.spyOn(repo, 'remove').mockResolvedValue(mockLanguage as any);

      const result = await service.remove(1);
      expect(result).toEqual(mockLanguage);
      expect(repo.remove).toHaveBeenCalledWith(mockLanguage);
    });

    it('should throw NotFoundException if language doesn’t exist (Remove non-existent)', async () => {
      jest.spyOn(repo, 'findOneBy').mockResolvedValue(null);
      await expect(service.remove(99)).rejects.toThrow(NotFoundException);
      expect(repo.remove).not.toHaveBeenCalled();
    });
  });
});