import { Request, Response, NextFunction } from 'express';
import { verifyToken, JwtPayload } from '../lib/jwt';
import { UnauthorizedError, ForbiddenError } from '../lib/errors';

declare global {
  namespace Express {
    interface Request {
      user?: JwtPayload;
    }
  }
}

export function authenticate(req: Request, _res: Response, next: NextFunction) {
  const header = req.headers.authorization;
  if (!header || !header.startsWith('Bearer ')) {
    return next(new UnauthorizedError('Token não fornecido'));
  }

  try {
    const token = header.split(' ')[1];
    req.user = verifyToken(token);
    next();
  } catch {
    next(new UnauthorizedError('Token inválido ou expirado'));
  }
}

export function authorize(...roles: string[]) {
  return (req: Request, _res: Response, next: NextFunction) => {
    if (!req.user) {
      return next(new UnauthorizedError());
    }
    if (!roles.includes(req.user.role)) {
      return next(new ForbiddenError());
    }
    next();
  };
}

export function authorizeTenantAccess(req: Request, _res: Response, next: NextFunction) {
  if (!req.user) return next(new UnauthorizedError());
  const { tenantId } = req.params;

  if (req.user.role === 'SUPER_ADMIN') return next();

  if (tenantId && req.user.tenantId !== tenantId) {
    return next(new ForbiddenError('Sem acesso a este negócio'));
  }
  next();
}
