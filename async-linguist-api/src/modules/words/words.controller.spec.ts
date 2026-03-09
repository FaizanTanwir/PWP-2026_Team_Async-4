import { Test, TestingModule } from '@nestjs/testing';
import { WordsController } from './words.controller';
import { WordsService } from './words.service';

describe('WordsController', () => {
  let controller: WordsController;
  let service: WordsService;

  const mockWord = { id: 1, term: 'Minä', lemma: 'minä', translation: 'I' };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [WordsController],
      providers: [
        {
          provide: WordsService,
          useValue: {
            create: jest.fn().mockResolvedValue(mockWord),
            findAll: jest.fn().mockResolvedValue([mockWord]),
            findOne: jest.fn().mockResolvedValue(mockWord),
            update: jest.fn().mockResolvedValue(mockWord),
            remove: jest.fn().mockResolvedValue(mockWord),
          },
        },
      ],
    }).compile();

    controller = module.get<WordsController>(WordsController);
    service = module.get<WordsService>(WordsService);
  });

  it('should call service.findAll for GET /words', async () => {
    expect(await controller.findAll()).toEqual([mockWord]);
    expect(service.findAll).toHaveBeenCalled();
  });

  it('should call service.findOne with a numeric ID', async () => {
    await controller.findOne(1);
    expect(service.findOne).toHaveBeenCalledWith(1);
  });
});