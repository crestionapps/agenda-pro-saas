import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate, authorizeTenantAccess } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError } from '../lib/errors';

export const professionalsRouter = Router();

const createProfessionalSchema = z.object({
  name: z.string().min(2),
  email: z.string().email().optional().nullable(),
  phone: z.string().optional().nullable(),
  bio: z.string().optional().nullable(),
  photo: z.string().optional().nullable(),
  specialties: z.array(z.string()).default([]),
});

const updateProfessionalSchema = createProfessionalSchema.partial();

professionalsRouter.get('/tenant/:tenantId', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const professionals = await prisma.professional.findMany({
      where: { tenantId: req.params.tenantId, isActive: true },
      include: {
        availabilities: true,
        _count: { select: { appointments: true } },
      },
      orderBy: { name: 'asc' },
    });
    res.json(professionals);
  } catch (e) { next(e); }
});

professionalsRouter.get('/:id', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const professional = await prisma.professional.findUnique({
      where: { id: req.params.id },
      include: {
        availabilities: true,
        services: true,
        _count: { select: { appointments: true } },
      },
    });
    if (!professional) throw new NotFoundError('Profissional');
    res.json(professional);
  } catch (e) { next(e); }
});

professionalsRouter.post('/tenant/:tenantId', authenticate, authorizeTenantAccess, validate(createProfessionalSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const professional = await prisma.professional.create({
      data: { ...req.body, tenantId: req.params.tenantId },
    });
    res.status(201).json(professional);
  } catch (e) { next(e); }
});

professionalsRouter.put('/:id', authenticate, validate(updateProfessionalSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const professional = await prisma.professional.update({
      where: { id: req.params.id },
      data: req.body,
    });
    res.json(professional);
  } catch (e) { next(e); }
});

professionalsRouter.delete('/:id', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    await prisma.professional.update({
      where: { id: req.params.id },
      data: { isActive: false },
    });
    res.json({ success: true });
  } catch (e) { next(e); }
});

professionalsRouter.put('/:id/availability', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { availabilities } = req.body;
    await prisma.availability.deleteMany({ where: { professionalId: req.params.id } });
    const created = await prisma.availability.createMany({
      data: availabilities.map((a: any) => ({ ...a, professionalId: req.params.id })),
    });
    res.json({ created: created.count });
  } catch (e) { next(e); }
});

professionalsRouter.get('/:id/availability', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const availabilities = await prisma.availability.findMany({
      where: { professionalId: req.params.id },
      orderBy: { dayOfWeek: 'asc' },
    });
    res.json(availabilities);
  } catch (e) { next(e); }
});
