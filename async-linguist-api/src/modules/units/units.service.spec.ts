import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { UnitsService } from './units.service';
import { Unit } from '../../entities/unit.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('UnitsService', () => {
  let service: UnitsService;
  let repo: Repository<Unit>;

  const mockUnit = {
    id: 1,
    title: 'Basic Greetings',
    course: { id: 10, title: 'Finnish 101' },
    sentences: [],
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
        UnitsService,
        {
          provide: getRepositoryToken(Unit),
          useValue: mockRepository,
        },
      ],
    }).compile();

    service = module.get<UnitsService>(UnitsService);
    repo = module.get<Repository<Unit>>(getRepositoryToken(Unit));
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  // --- CREATE ---
  describe('create', () => {
    it('should successfully create a unit linked to a course', async () => {
      const dto = { title: 'New Unit', courseId: 10 };
      const savedUnit = { id: 1, ...dto, course: { id: 10 } };
      
      jest.spyOn(repo, 'save').mockResolvedValue(savedUnit as any);

      const result = await service.create(dto);
      
      expect(repo.create).toHaveBeenCalledWith({
        title: dto.title,
        course: { id: dto.courseId },
      });
      expect(result).toEqual(savedUnit);
    });
  });

  // --- FIND ALL ---
  describe('findAll', () => {
    it('should return units with course and sentences relations', async () => {
      jest.spyOn(repo, 'find').mockResolvedValue([mockUnit] as any);
      
      const result = await service.findAll();
      
      expect(repo.find).toHaveBeenCalledWith({ 
        relations: ['course', 'sentences'] 
      });
      expect(result).toEqual([mockUnit]);
    });
  });

  // --- FIND ONE ---
  describe('findOne', () => {
    it('should return a unit if found', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockUnit as any);
      
      const result = await service.findOne(1);
      
      expect(result).toEqual(mockUnit);
    });

    it('should throw NotFoundException if unit does not exist', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      
      await expect(service.findOne(999)).rejects.toThrow(NotFoundException);
    });
  });

  // --- UPDATE (EDGE CASES) ---
  describe('update', () => {
    it('should update unit title only', async () => {
      const updateDto = { title: 'Updated Title' };
      jest.spyOn(repo, 'preload').mockResolvedValue({ ...mockUnit, ...updateDto } as any);
      jest.spyOn(repo, 'save').mockResolvedValue({ ...mockUnit, ...updateDto } as any);

      await service.update(1, updateDto);
      
      expect(repo.preload).toHaveBeenCalledWith({
        id: 1,
        title: 'Updated Title',
        course: undefined, // Verify course remains untouched if not in DTO
      });
    });

    it('should update the associated course if courseId is provided', async () => {
      const updateDto = { courseId: 20 };
      jest.spyOn(repo, 'preload').mockResolvedValue({ ...mockUnit, course: { id: 20 } } as any);
      jest.spyOn(repo, 'save').mockResolvedValue({ ...mockUnit, course: { id: 20 } } as any);

      await service.update(1, updateDto);
      
      expect(repo.preload).toHaveBeenCalledWith({
        id: 1,
        title: undefined,
        course: { id: 20 },
      });
    });

    it('should throw NotFoundException if preload fails', async () => {
      jest.spyOn(repo, 'preload').mockResolvedValue(null);
      
      await expect(service.update(999, { title: 'New' })).rejects.toThrow(NotFoundException);
    });
  });

  // --- REMOVE ---
  describe('remove', () => {
    it('should remove an existing unit', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockUnit as any);
      jest.spyOn(repo, 'remove').mockResolvedValue(mockUnit as any);

      const result = await service.remove(1);
      
      expect(repo.remove).toHaveBeenCalledWith(mockUnit);
      expect(result).toEqual(mockUnit);
    });

    it('should throw NotFoundException if unit to remove is not found', async () => {
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      
      await expect(service.remove(999)).rejects.toThrow(NotFoundException);
      expect(repo.remove).not.toHaveBeenCalled();
    });
  });
});