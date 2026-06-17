import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate, authorize, authorizeTenantAccess } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError, ConflictError } from '../lib/errors';

export const tenantsRouter = Router();

const createTenantSchema = z.object({
  name: z.string().min(2, 'Nome deve ter pelo menos 2 caracteres'),
  slug: z.string().min(2, 'Slug deve ter pelo menos 2 caracteres').regex(/^[a-z0-9-]+$/, 'Slug deve conter apenas letras minúsculas, números e hífens'),
  type: z.enum(['BARBERSHOP', 'HAIRDRESSER', 'SPA', 'NAILS', 'AESTHETICS', 'CLINIC']),
  description: z.string().optional(),
  address: z.string().optional(),
  phone: z.string().optional(),
  email: z.string().email().optional(),
});

const updateTenantSchema = z.object({
  name: z.string().min(2).optional(),
  description: z.string().optional(),
  address: z.string().optional(),
  phone: z.string().optional(),
  email: z.string().email().optional(),
  logo: z.string().optional(),
  coverImage: z.string().optional(),
  isActive: z.boolean().optional(),
});

tenantsRouter.get('/', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { search, type, page = '1', limit = '20' } = req.query;
    const skip = (parseInt(page as string) - 1) * parseInt(limit as string);
    const take = parseInt(limit as string);

    const where: any = { isActive: true };

    if (search) {
      where.OR = [
        { name: { contains: search as string, mode: 'insensitive' } },
        { address: { contains: search as string, mode: 'insensitive' } },
        { description: { contains: search as string, mode: 'insensitive' } },
      ];
    }
    if (type) where.type = type;

    const [tenants, total] = await Promise.all([
      prisma.tenant.findMany({
        where,
        skip,
        take,
        orderBy: { name: 'asc' },
        include: { _count: { select: { reviews: true, professionals: true } } },
      }),
      prisma.tenant.count({ where }),
    ]);

    res.json({
      tenants: tenants.map(t => ({
        ...t,
        reviewCount: t._count.reviews,
        professionalCount: t._count.professionals,
        _count: undefined,
      })),
      total,
      page: parseInt(page as string),
      totalPages: Math.ceil(total / take),
    });
  } catch (e) { next(e); }
});

tenantsRouter.get('/:slug', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const tenant = await prisma.tenant.findUnique({
      where: { slug: req.params.slug },
      include: {
        professionals: {
          where: { isActive: true },
          include: {
            serviceProfessionals: {
              include: { service: { select: { id: true, name: true, category: true } } },
            },
          },
        },
        services: {
          where: { isActive: true },
          include: {
            serviceProfessionals: {
              include: { professional: { select: { id: true, name: true, photo: true } } },
            },
          },
          orderBy: [{ category: 'asc' }, { name: 'asc' }],
        },
        businessHours: { orderBy: { dayOfWeek: 'asc' } },
        reviews: { include: { customer: { select: { name: true, avatar: true } } }, orderBy: { createdAt: 'desc' }, take: 20 },
      },
    });
    if (!tenant) throw new NotFoundError('Negócio');
    res.json({
      ...tenant,
      services: tenant.services.map(s => ({
        ...s,
        professionals: s.serviceProfessionals.map(sp => sp.professional),
        serviceProfessionals: undefined,
      })),
      professionals: tenant.professionals.map(p => ({
        ...p,
        services: p.serviceProfessionals.map(sp => sp.service),
        serviceProfessionals: undefined,
      })),
    });
  } catch (e) { next(e); }
});

tenantsRouter.post('/', authenticate, authorize('SUPER_ADMIN'), validate(createTenantSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const existing = await prisma.tenant.findUnique({ where: { slug: req.body.slug } });
    if (existing) throw new ConflictError('Slug já está em uso');

    const tenant = await prisma.tenant.create({ data: req.body });
    res.status(201).json(tenant);
  } catch (e) { next(e); }
});

tenantsRouter.put('/:tenantId', authenticate, authorizeTenantAccess, validate(updateTenantSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const tenant = await prisma.tenant.update({
      where: { id: req.params.tenantId },
      data: req.body,
    });
    res.json(tenant);
  } catch (e) { next(e); }
});

tenantsRouter.put('/:tenantId/hours', authenticate, authorizeTenantAccess, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { hours } = req.body;
    await prisma.businessHour.deleteMany({ where: { tenantId: req.params.tenantId } });
    const created = await prisma.businessHour.createMany({
      data: hours.map((h: any) => ({ ...h, tenantId: req.params.tenantId })),
    });
    res.json({ created: created.count });
  } catch (e) { next(e); }
});

tenantsRouter.get('/:tenantId/hours', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const hours = await prisma.businessHour.findMany({
      where: { tenantId: req.params.tenantId },
      orderBy: { dayOfWeek: 'asc' },
    });
    res.json(hours);
  } catch (e) { next(e); }
});

tenantsRouter.get('/:tenantId/stats', authenticate, authorizeTenantAccess, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { tenantId } = req.params;
    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

    const [totalAppointments, monthlyAppointments, totalProfessionals, totalCustomers] = await Promise.all([
      prisma.appointment.count({ where: { tenantId } }),
      prisma.appointment.count({ where: { tenantId, createdAt: { gte: startOfMonth } } }),
      prisma.professional.count({ where: { tenantId, isActive: true } }),
      prisma.appointment.groupBy({ by: ['customerId'], where: { tenantId }, _count: true }),
    ]);

    res.json({
      totalAppointments,
      monthlyAppointments,
      totalProfessionals,
      totalCustomers: totalCustomers.length,
      completionRate: 0,
    });
  } catch (e) { next(e); }
});
