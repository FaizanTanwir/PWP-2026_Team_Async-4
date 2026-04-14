// src/users/users.service.ts
import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { User } from '../entities/user.entity';
import * as bcrypt from 'bcrypt';

@Injectable()
export class UsersService {
  constructor(
    @InjectRepository(User)
    private usersRepository: Repository<User>,
  ) {}

  async create(userData: Partial<User>) {
  // Ensure the password exists before hashing to avoid undefined errors
    if (userData.password) {
      const salt = await bcrypt.genSalt();
      // ADD 'await' HERE:
      userData.password = await bcrypt.hash(userData.password, salt);
    }
    return this.usersRepository.save(userData);
  }

  async findOneByEmail(email: string): Promise<User | null> {
    return this.usersRepository.findOne({ where: { email } });
  }
}