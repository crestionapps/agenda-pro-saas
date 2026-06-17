import { Request, Response, NextFunction } from 'express';
import { ZodSchema } from 'zod';
import { ValidationError } from '../lib/errors';

export function validate(schema: ZodSchema, source: 'body' | 'query' | 'params' = 'body') {
  return (req: Request, _res: Response, next: NextFunction) => {
    const result = schema.safeParse(req[source]);
    if (!result.success) {
      const messages = result.error.issues.map(i => i.message).join(', ');
      return next(new ValidationError(messages));
    }
    req[source] = result.data;
    next();
  };
}
