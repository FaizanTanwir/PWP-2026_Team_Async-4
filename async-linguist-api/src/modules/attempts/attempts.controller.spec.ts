import { Test, TestingModule } from '@nestjs/testing';
import { AttemptsController } from './attempts.controller';
import { AttemptsService } from './attempts.service';

describe('AttemptsController', () => {
  let controller: AttemptsController;
  let service: AttemptsService;

  const mockAttempt = { id: 1, score: 95, audio_url: 'url' };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [AttemptsController],
      providers: [
        {
          provide: AttemptsService,
          useValue: {
            create: jest.fn().mockResolvedValue(mockAttempt),
            findAll: jest.fn().mockResolvedValue([mockAttempt]),
            findOne: jest.fn().mockResolvedValue(mockAttempt),
            remove: jest.fn().mockResolvedValue(mockAttempt),
          },
        },
      ],
    }).compile();

    controller = module.get<AttemptsController>(AttemptsController);
    service = module.get<AttemptsService>(AttemptsService);
  });

  it('should call service.create with correct data', async () => {
    const dto = { audio_url: 'url', score: 95, sentenceId: 1 };
    expect(await controller.create(dto)).toEqual(mockAttempt);
    expect(service.create).toHaveBeenCalledWith(dto);
  });

  it('should call service.findOne with numeric id', async () => {
    await controller.findOne(1);
    expect(service.findOne).toHaveBeenCalledWith(1);
  });
});