import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy) {
  constructor() { // 👈 Removed ConfigService injection
    super({
      jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
      ignoreExpiration: false,
      secretOrKey: process.env.JWT_SECRET, // 👈 Now this will be defined
    });
  }

  async validate(payload: any) {
    console.log('JWT Payload decoded successfully:', payload);
    return { userId: payload.sub, email: payload.email, role: payload.role };
  }
}