// src/entities/user.entity.ts
import { Entity, PrimaryGeneratedColumn, Column, OneToMany } from 'typeorm';
import { ApiProperty } from '@nestjs/swagger';
import { Attempt } from './attempt.entity';
import { Course } from './course.entity';

export enum UserRole {
  STUDENT = 'student',
  TEACHER = 'teacher',
  ADMIN = 'admin',
}

@Entity('users')
export class User {
  @ApiProperty({ example: 1 })
  @PrimaryGeneratedColumn()
  id: number;

  @ApiProperty({ example: 'student@example.com' })
  @Column({ unique: true })
  email: string;

  @Column()
  password: string; 

  @ApiProperty({ example: UserRole.STUDENT, enum: UserRole })
  @Column({ type: 'enum', enum: UserRole, default: UserRole.STUDENT })
  role: UserRole;

  // Relation: One user can have many attempts
  @OneToMany(() => Attempt, (attempt) => attempt.user)
  attempts: Attempt[];

  // Relation: One user (Teacher) can create many courses
  @OneToMany(() => Course, (course) => course.createdBy)
  courses: Course[];
}