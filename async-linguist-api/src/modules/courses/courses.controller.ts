// src/courses/courses.controller.ts
import { Controller, Get, Post, Body, Patch, Param, Delete, ParseIntPipe, HttpCode, HttpStatus } from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiParam, ApiBody } from '@nestjs/swagger';
import { CoursesService } from './courses.service';
import { CreateCourseDto } from './dto/create-course.dto';
import { UpdateCourseDto } from './dto/update-course.dto';
import { Course } from '../../entities/course.entity';

@ApiTags('Courses') // Grouping for Swagger UI
@Controller('courses')
export class CoursesController {
  constructor(private readonly coursesService: CoursesService) {}

  @Post()
  @ApiOperation({ summary: 'Create a new course' })
  @ApiBody({ type: CreateCourseDto }) // Forces Swagger to show the request schema
  @ApiResponse({ status: 201, description: 'Created', type: Course })
  @ApiResponse({ status: 400, description: 'Bad Request' })
  create(@Body() createCourseDto: CreateCourseDto) {
    return this.coursesService.create(createCourseDto);
  }

  @Get()
  @ApiOperation({ summary: 'Get all courses' })
  @ApiResponse({ status: 200, description: 'Return all courses.', type: [Course] })
  findAll() {
    return this.coursesService.findAll();
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get a course by ID' })
  @ApiParam({ name: 'id', description: 'The unique identifier of the course', example: 1 })
  @ApiResponse({ status: 200, description: 'The course found.', type: Course })
  @ApiResponse({ status: 404, description: 'Course not found.' })
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.coursesService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update a course partially' })
  @ApiResponse({ status: 200, description: 'Updated', type: Course })
  @ApiResponse({ status: 404, description: 'Not Found' })
  update(@Param('id', ParseIntPipe) id: number, @Body() updateCourseDto: UpdateCourseDto) {
    return this.coursesService.update(id, updateCourseDto);
  }

  @Delete(':id')
  @HttpCode(HttpStatus.NO_CONTENT)
  @ApiOperation({ summary: 'Delete a course' })
  @ApiResponse({ status: 204, description: 'Deleted' })
  @ApiResponse({ status: 404, description: 'Not Found' })
  remove(@Param('id', ParseIntPipe) id: number) {
    return this.coursesService.remove(id);
  }
}