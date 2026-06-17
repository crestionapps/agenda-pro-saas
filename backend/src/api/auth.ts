import { Router, Request, Response, NextFunction } from 'express';
import bcrypt from 'bcryptjs';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { signToken } from '../lib/jwt';
import { validate } from '../middleware/validate';
import { authenticate } from '../middleware/auth';
import { NotFoundError, ConflictError, UnauthorizedError, ValidationError } from '../lib/errors';

export const authRouter = Router();

const registerSchema = z.object({
  name: z.string().min(2, 'Nome deve ter pelo menos 2 caracteres'),
  email: z.string().email('Email inválido'),
  password: z.string().min(6, 'Senha deve ter pelo menos 6 caracteres'),
  phone: z.string().optional(),
});

const loginSchema = z.object({
  email: z.string().email('Email inválido'),
  password: z.string().min(1, 'Senha é obrigatória'),
});

authRouter.post('/register', validate(registerSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { name, email, password, phone } = req.body;

    const existing = await prisma.user.findUnique({ where: { email } });
    if (existing) throw new ConflictError('Email já registado');

    const hashedPassword = await bcrypt.hash(password, 10);
    const user = await prisma.user.create({
      data: { name, email, password: hashedPassword, phone, role: 'CUSTOMER' },
    });

    const token = signToken({ userId: user.id, email: user.email, role: user.role, tenantId: user.tenantId });
    res.status(201).json({
      token,
      user: { id: user.id, name: user.name, email: user.email, role: user.role, phone: user.phone, avatar: user.avatar },
    });
  } catch (e) { next(e); }
});

authRouter.post('/login', validate(loginSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { email, password } = req.body;

    const user = await prisma.user.findUnique({ where: { email } });
    if (!user) throw new UnauthorizedError('Email ou senha inválidos');

    const valid = await bcrypt.compare(password, user.password);
    if (!valid) throw new UnauthorizedError('Email ou senha inválidos');

    if (!user.isActive) throw new UnauthorizedError('Conta desativada');

    const token = signToken({ userId: user.id, email: user.email, role: user.role, tenantId: user.tenantId });
    res.json({
      token,
      user: { id: user.id, name: user.name, email: user.email, role: user.role, tenantId: user.tenantId, phone: user.phone, avatar: user.avatar },
    });
  } catch (e) { next(e); }
});

authRouter.get('/me', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const user = await prisma.user.findUnique({
      where: { id: req.user!.userId },
      include: { tenant: true },
    });
    if (!user) throw new NotFoundError('Utilizador');
    res.json({
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role,
      phone: user.phone,
      avatar: user.avatar,
      tenantId: user.tenantId,
      tenant: user.tenant,
    });
  } catch (e) { next(e); }
});

authRouter.put('/profile', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { name, phone, avatar } = req.body;
    const user = await prisma.user.update({
      where: { id: req.user!.userId },
      data: { name, phone, avatar },
    });
    res.json({ id: user.id, name: user.name, email: user.email, role: user.role, phone: user.phone, avatar: user.avatar });
  } catch (e) { next(e); }
});
