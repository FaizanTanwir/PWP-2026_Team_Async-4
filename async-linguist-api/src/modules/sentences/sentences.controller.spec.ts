import { Test, TestingModule } from '@nestjs/testing';
import { SentencesController } from './sentences.controller';
import { SentencesService } from './sentences.service';
import { CreateSentenceDto } from './dto/create-sentence.dto';
import { UpdateSentenceDto } from './dto/update-sentence.dto';

describe('SentencesController', () => {
  let controller: SentencesController;
  let service: SentencesService;

  const mockSentence = {
    id: 1,
    text_target: 'Terve!',
    text_source: 'Hello!',
    unit: { id: 1 },
  };

  const mockSentencesService = {
    create: jest.fn().mockResolvedValue(mockSentence),
    findAll: jest.fn().mockResolvedValue([mockSentence]),
    findOne: jest.fn().mockResolvedValue(mockSentence),
    update: jest.fn().mockResolvedValue({ ...mockSentence, text_target: 'Moi!' }),
    remove: jest.fn().mockResolvedValue(mockSentence),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [SentencesController],
      providers: [
        {
          provide: SentencesService,
          useValue: mockSentencesService,
        },
      ],
    }).compile();

    controller = module.get<SentencesController>(SentencesController);
    service = module.get<SentencesService>(SentencesService);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });

  describe('create', () => {
    it('should call service.create with correct DTO', async () => {
      const dto: CreateSentenceDto = { 
        text_target: 'Terve!', 
        text_source: 'Hello!', 
        unitId: 1 
      };
      const result = await controller.create(dto);
      
      expect(service.create).toHaveBeenCalledWith(dto);
      expect(result).toEqual(mockSentence);
    });
  });

  describe('findAll', () => {
    it('should return an array of sentences', async () => {
      const result = await controller.findAll();
      expect(service.findAll).toHaveBeenCalled();
      expect(result).toEqual([mockSentence]);
    });
  });

  describe('findOne', () => {
    it('should return a single sentence by ID', async () => {
      const result = await controller.findOne(1);
      expect(service.findOne).toHaveBeenCalledWith(1);
      expect(result).toEqual(mockSentence);
    });
  });

  describe('update', () => {
    it('should call service.update with ID and partial DTO', async () => {
      const dto: UpdateSentenceDto = { text_target: 'Moi!' };
      const result = await controller.update(1, dto);
      
      expect(service.update).toHaveBeenCalledWith(1, dto);
      expect(result.text_target).toEqual('Moi!');
    });
  });

  describe('remove', () => {
    it('should call service.remove', async () => {
      const result = await controller.remove(1);
      expect(service.remove).toHaveBeenCalledWith(1);
      expect(result).toEqual(mockSentence);
    });
  });
});