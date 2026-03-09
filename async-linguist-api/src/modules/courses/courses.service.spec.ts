import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { CoursesService } from './courses.service';
import { Course } from '../../entities/course.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('CoursesService', () => {
  let service: CoursesService;
  let repo: Repository<Course>;

  // Mock data including relations to mimic your service logic
  const mockCourse = {
    id: 1,
    title: 'Finnish for Beginners',
    sourceLanguageId: 1,
    targetLanguageId: 2,
    sourceLanguage: { id: 1, name: 'English' },
    targetLanguage: { id: 2, name: 'Finnish' },
    units: [],
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
        CoursesService,
        {
          provide: getRepositoryToken(Course),
          useValue: mockRepository,
        },
      ],
    }).compile();

    service = module.get<CoursesService>(CoursesService);
    repo = module.get<Repository<Course>>(getRepositoryToken(Course));
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  // --- FIND ALL ---
  describe('findAll', () => {
    it('should return all courses with their relations', async () => {
      // Testing: Ensure relations array is passed to the find method
      jest.spyOn(repo, 'find').mockResolvedValue([mockCourse] as any);

      const result = await service.findAll();

      expect(result).toEqual([mockCourse]);
      expect(repo.find).toHaveBeenCalledWith({
        relations: ['sourceLanguage', 'targetLanguage', 'units'],
      });
    });

    it('should return empty array if no courses exist', async () => {
      jest.spyOn(repo, 'find').mockResolvedValue([]);
      const result = await service.findAll();
      expect(result).toEqual([]);
    });
  });

  // --- CREATE ---
  describe('create', () => {
    it('should successfully create a course', async () => {
      // Testing: Normal creation path
      const dto = {
        title: 'New Course',
        sourceLanguageId: 1,
        targetLanguageId: 2,
      };
      jest.spyOn(repo, 'save').mockResolvedValue({ id: 10, ...dto } as any);

      const result = await service.create(dto);
      expect(result.id).toBe(10);
      expect(repo.create).toHaveBeenCalledWith(dto);
      expect(repo.save).toHaveBeenCalled();
    });
  });

  // --- FIND ONE ---
  describe('findOne', () => {
    it('should return a specific course with relations', async () => {
      // Testing: Successful retrieval by ID
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockCourse as any);

      const result = await service.findOne(1);
      expect(result).toEqual(mockCourse);
      expect(repo.findOne).toHaveBeenCalledWith({
        where: { id: 1 },
        relations: ['sourceLanguage', 'targetLanguage', 'units'],
      });
    });

    it('should throw NotFoundException if course does not exist', async () => {
      // Testing: 404 error path
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);
      await expect(service.findOne(999)).rejects.toThrow(NotFoundException);
    });
  });

  // --- UPDATE ---
  describe('update', () => {
    it('should update a course using preload', async () => {
      // Testing: Normal update path using TypeORM preload
      const updateDto = { title: 'Updated Title' };
      jest
        .spyOn(repo, 'preload')
        .mockResolvedValue({ ...mockCourse, ...updateDto } as any);
      jest
        .spyOn(repo, 'save')
        .mockResolvedValue({ ...mockCourse, ...updateDto } as any);

      const result = await service.update(1, updateDto);
      expect(result.title).toBe('Updated Title');
      expect(repo.preload).toHaveBeenCalled();
    });

    it('should throw NotFoundException if preloading fails (record not found)', async () => {
      // Testing: Update fails if ID is invalid
      jest.spyOn(repo, 'preload').mockResolvedValue(null);
      await expect(service.update(999, { title: 'Ghost' })).rejects.toThrow(
        NotFoundException,
      );
    });
  });

  // --- REMOVE ---
  describe('remove', () => {
    it('should remove an existing course', async () => {
      // Testing: Normal removal
      // We mock findOne to return the course first
      jest.spyOn(repo, 'findOne').mockResolvedValue(mockCourse as any);

      // FIX: Use mockCourse here instead of the undefined mockLanguage
      jest.spyOn(repo, 'remove').mockResolvedValue(mockCourse as any);

      const result = await service.remove(1);

      expect(result).toEqual(mockCourse);
      expect(repo.remove).toHaveBeenCalledWith(mockCourse);
    });

    it('should throw NotFoundException if trying to remove non-existent course', async () => {
      // Testing: Remove guard check
      jest.spyOn(repo, 'findOne').mockResolvedValue(null);

      await expect(service.remove(999)).rejects.toThrow(NotFoundException);

      // Ensure the database 'remove' was never even called
      expect(repo.remove).not.toHaveBeenCalled();
    });
  });
});
