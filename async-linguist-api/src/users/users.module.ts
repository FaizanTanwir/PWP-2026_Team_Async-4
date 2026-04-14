import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { UsersService } from './users.service';
import { User } from '../entities/user.entity'; // Adjust path if needed

@Module({
  // 1. Give this module access to the User database table
  imports: [TypeOrmModule.forFeature([User])], 
  providers: [UsersService],
  // 2. EXPORT the service so the AuthModule can use it
  exports: [UsersService], 
})
export class UsersModule {}