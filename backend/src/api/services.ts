import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate, authorizeTenantAccess } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError } from '../lib/errors';

export const servicesRouter = Router();

const createServiceSchema = z.object({
  name: z.string().min(2),
  description: z.string().optional().nullable(),
  category: z.string().optional().nullable(),
  duration: z.number().int().min(5, 'Duração mínima de 5 minutos'),
  price: z.number().min(0, 'Preço não pode ser negativo'),
  professionalIds: z.array(z.string()).optional().default([]),
});

const updateServiceSchema = createServiceSchema.partial();

servicesRouter.get('/tenant/:tenantId', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const services = await prisma.service.findMany({
      where: { tenantId: req.params.tenantId, isActive: true },
      include: {
        serviceProfessionals: {
          include: { professional: { select: { id: true, name: true, photo: true } } },
        },
      },
      orderBy: [{ category: 'asc' }, { name: 'asc' }],
    });
    res.json(services.map(s => ({ ...s, professionals: s.serviceProfessionals.map(sp => sp.professional), serviceProfessionals: undefined })));
  } catch (e) { next(e); }
});

servicesRouter.get('/:id', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const service = await prisma.service.findUnique({
      where: { id: req.params.id },
      include: {
        serviceProfessionals: {
          include: { professional: { select: { id: true, name: true, photo: true } } },
        },
      },
    });
    if (!service) throw new NotFoundError('Serviço');
    res.json({ ...service, professionals: service.serviceProfessionals.map(sp => sp.professional), serviceProfessionals: undefined });
  } catch (e) { next(e); }
});

servicesRouter.post('/tenant/:tenantId', authenticate, authorizeTenantAccess, validate(createServiceSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { professionalIds, ...data } = req.body;
    const service = await prisma.service.create({
      data: {
        ...data,
        tenantId: req.params.tenantId,
        serviceProfessionals: {
          create: (professionalIds as string[]).map((pid: string) => ({ professionalId: pid })),
        },
      },
      include: {
        serviceProfessionals: {
          include: { professional: { select: { id: true, name: true, photo: true } } },
        },
      },
    });
    res.status(201).json({ ...service, professionals: service.serviceProfessionals.map(sp => sp.professional), serviceProfessionals: undefined });
  } catch (e) { next(e); }
});

servicesRouter.put('/:id', authenticate, validate(updateServiceSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { professionalIds, ...data } = req.body;
    if (professionalIds) {
      await prisma.serviceProfessional.deleteMany({ where: { serviceId: req.params.id } });
      await prisma.serviceProfessional.createMany({
        data: (professionalIds as string[]).map((pid: string) => ({ serviceId: req.params.id, professionalId: pid })),
      });
    }
    const service = await prisma.service.update({
      where: { id: req.params.id },
      data,
      include: {
        serviceProfessionals: {
          include: { professional: { select: { id: true, name: true, photo: true } } },
        },
      },
    });
    res.json({ ...service, professionals: service.serviceProfessionals.map(sp => sp.professional), serviceProfessionals: undefined });
  } catch (e) { next(e); }
});

servicesRouter.delete('/:id', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    await prisma.service.update({
      where: { id: req.params.id },
      data: { isActive: false },
    });
    res.json({ success: true });
  } catch (e) { next(e); }
});
