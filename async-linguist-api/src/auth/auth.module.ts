import { Module } from '@nestjs/common';
import { JwtModule } from '@nestjs/jwt';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthController } from './auth.controller';
import { AuthService } from './auth.service';
import { UsersModule } from '../users/users.module'; // Adjust path if needed

@Module({
  imports: [
    UsersModule, // Brings in the exported UsersService
    
    // Configure JWT asynchronously so it can read from your .env file
    JwtModule.registerAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: async (configService: ConfigService) => ({
        secret: configService.get<string>('JWT_SECRET') || 'fallback_dev_secret',
        signOptions: { expiresIn: '60m' }, // Token expires in 1 hour
      }),
    }),
  ],
  controllers: [AuthController], // This is what exposes the routes!
  providers: [AuthService],
})
export class AuthModule {}