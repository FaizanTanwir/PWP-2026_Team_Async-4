import { Test, TestingModule } from '@nestjs/testing';
import { LanguagesController } from './languages.controller';
import { LanguagesService } from './languages.service';
import { CreateLanguageDto } from './dto/create-language.dto';
import { UpdateLanguageDto } from './dto/update-language.dto';

describe('LanguagesController', () => {
  let controller: LanguagesController;
  let service: LanguagesService;

  const mockLanguage = {
    id: 1,
    name: 'Finnish',
    code: 'fi',
  };

  const mockLanguagesService = {
    create: jest.fn().mockResolvedValue(mockLanguage),
    findAll: jest.fn().mockResolvedValue([mockLanguage]),
    findOne: jest.fn().mockResolvedValue(mockLanguage),
    update: jest.fn().mockResolvedValue({ ...mockLanguage, name: 'Suomi' }),
    remove: jest.fn().mockResolvedValue(undefined), // No Content returns undefined
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [LanguagesController],
      providers: [
        {
          provide: LanguagesService,
          useValue: mockLanguagesService,
        },
      ],
    }).compile();

    controller = module.get<LanguagesController>(LanguagesController);
    service = module.get<LanguagesService>(LanguagesService);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });

  describe('create', () => {
    it('should call service.create with the correct DTO', async () => {
      const dto: CreateLanguageDto = { name: 'Finnish', code: 'fi' };
      const result = await controller.create(dto);

      expect(service.create).toHaveBeenCalledWith(dto);
      expect(result).toEqual(mockLanguage);
    });
  });

  describe('findAll', () => {
    it('should return an array of languages', async () => {
      const result = await controller.findAll();
      expect(service.findAll).toHaveBeenCalled();
      expect(result).toEqual([mockLanguage]);
    });
  });

  describe('findOne', () => {
    it('should return a single language by id', async () => {
      const result = await controller.findOne(1);
      expect(service.findOne).toHaveBeenCalledWith(1);
      expect(result).toEqual(mockLanguage);
    });
  });

  describe('update', () => {
    it('should call service.update with id and partial data', async () => {
      const dto: UpdateLanguageDto = { name: 'Suomi' };
      const result = await controller.update(1, dto);

      expect(service.update).toHaveBeenCalledWith(1, dto);
      expect(result.name).toEqual('Suomi');
    });
  });

  describe('remove', () => {
    it('should call service.remove and return undefined (for No Content)', async () => {
      const result = await controller.remove(1);
      expect(service.remove).toHaveBeenCalledWith(1);
      expect(result).toBeUndefined();
    });
  });
});
