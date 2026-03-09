import { Test, TestingModule } from '@nestjs/testing';
import { UnitsController } from './units.controller';
import { UnitsService } from './units.service';
import { CreateUnitDto } from './dto/create-unit.dto';
import { UpdateUnitDto } from './dto/update-unit.dto';

describe('UnitsController', () => {
  let controller: UnitsController;
  let service: UnitsService;

  const mockUnit = {
    id: 1,
    title: 'Basic Finnish',
    course: { id: 10 },
  };

  const mockUnitsService = {
    create: jest.fn().mockResolvedValue(mockUnit),
    findAll: jest.fn().mockResolvedValue([mockUnit]),
    findOne: jest.fn().mockResolvedValue(mockUnit),
    update: jest.fn().mockResolvedValue({ ...mockUnit, title: 'Updated Unit' }),
    remove: jest.fn().mockResolvedValue(mockUnit),
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [UnitsController],
      providers: [
        {
          provide: UnitsService,
          useValue: mockUnitsService,
        },
      ],
    }).compile();

    controller = module.get<UnitsController>(UnitsController);
    service = module.get<UnitsService>(UnitsService);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });

  describe('create', () => {
    it('should successfully create a unit', async () => {
      const dto: CreateUnitDto = { title: 'Basic Finnish', courseId: 10 };
      const result = await controller.create(dto);

      expect(service.create).toHaveBeenCalledWith(dto);
      expect(result).toEqual(mockUnit);
    });
  });

  describe('findAll', () => {
    it('should return all units', async () => {
      const result = await controller.findAll();
      expect(service.findAll).toHaveBeenCalled();
      expect(result).toEqual([mockUnit]);
    });
  });

  describe('findOne', () => {
    it('should return a single unit based on ID', async () => {
      const result = await controller.findOne(1);
      expect(service.findOne).toHaveBeenCalledWith(1);
      expect(result).toEqual(mockUnit);
    });
  });

  describe('update', () => {
    it('should update unit details', async () => {
      const dto: UpdateUnitDto = { title: 'Updated Unit' };
      const result = await controller.update(1, dto);

      expect(service.update).toHaveBeenCalledWith(1, dto);
      expect(result.title).toEqual('Updated Unit');
    });
  });

  describe('remove', () => {
    it('should call the remove service method', async () => {
      const result = await controller.remove(1);
      expect(service.remove).toHaveBeenCalledWith(1);
      expect(result).toEqual(mockUnit);
    });
  });
});
