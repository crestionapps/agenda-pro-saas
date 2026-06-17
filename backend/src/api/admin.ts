import { Router, Request, Response, NextFunction } from 'express';
import { prisma } from '../lib/prisma';
import { authenticate, authorize } from '../middleware/auth';

export const adminRouter = Router();

adminRouter.get('/dashboard', authenticate, authorize('SUPER_ADMIN'), async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const now = new Date();
    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const startOfYear = new Date(now.getFullYear(), 0, 1);

    const [
      totalTenants,
      activeTenants,
      totalUsers,
      totalProfessionals,
      totalAppointments,
      monthlyAppointments,
      totalRevenue,
      monthlyRevenue,
      tenantsByType,
      appointmentsByStatus,
      recentAppointments,
      topTenants,
      subscriptionRevenue,
      activeSubscriptions,
      plansCount,
    ] = await Promise.all([
      prisma.tenant.count(),
      prisma.tenant.count({ where: { isActive: true } }),
      prisma.user.count(),
      prisma.professional.count(),
      prisma.appointment.count(),
      prisma.appointment.count({ where: { createdAt: { gte: startOfMonth } } }),
      prisma.appointment.aggregate({ _sum: { service: { price: true } } }).then(r => r._sum?.service?.price ?? 0),
      prisma.appointment.findMany({
        where: { createdAt: { gte: startOfMonth }, status: 'COMPLETED' },
        include: { service: { select: { price: true } } },
      }).then(apps => apps.reduce((sum, a) => sum + (a.service?.price ?? 0), 0)),
      prisma.tenant.groupBy({ by: ['type'], _count: true }),
      prisma.appointment.groupBy({ by: ['status'], _count: true }),
      prisma.appointment.findMany({
        take: 10,
        orderBy: { createdAt: 'desc' },
        include: {
          tenant: { select: { name: true, slug: true } },
          customer: { select: { name: true } },
          service: { select: { name: true, price: true } },
        },
      }),
      prisma.appointment.groupBy({
        by: ['tenantId'],
        _count: true,
        orderBy: { _count: { id: 'desc' } },
        take: 10,
      }).then(async groups => {
        const tenants = await prisma.tenant.findMany({
          where: { id: { in: groups.map(g => g.tenantId) } },
          select: { id: true, name: true, slug: true, type: true, logo: true },
        });
        return groups.map(g => ({
          ...tenants.find(t => t.id === g.tenantId),
          appointmentCount: g._count,
        }));
      }),
      prisma.subscriptionPlan.findMany({ where: { isActive: true }, select: { price: true, _count: { select: { tenantSubscriptions: true } } } })
        .then(plans => plans.reduce((sum, p) => sum + p.price * p._count.tenantSubscriptions, 0)),
      prisma.tenantSubscription.count({ where: { isActive: true } }),
      prisma.subscriptionPlan.count({ where: { isActive: true } }),
    ]);

    res.json({
      kpis: {
        totalTenants,
        activeTenants,
        totalUsers,
        totalProfessionals,
        totalAppointments,
        monthlyAppointments,
        totalRevenue: Math.round(totalRevenue * 100) / 100,
        monthlyRevenue: Math.round(monthlyRevenue * 100) / 100,
        subscriptionRevenue: Math.round(subscriptionRevenue * 100) / 100,
        activeSubscriptions,
        plansCount,
      },
      tenantsByType,
      appointmentsByStatus: appointmentsByStatus.map(a => ({ status: a.status, count: a._count })),
      recentAppointments,
      topTenants,
    });
  } catch (e) { next(e); }
});

adminRouter.get('/tenants', authenticate, authorize('SUPER_ADMIN'), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { page = '1', limit = '20', search } = req.query;
    const skip = (parseInt(page as string) - 1) * parseInt(limit as string);
    const take = parseInt(limit as string);
    const where: any = {};
    if (search) where.name = { contains: search as string, mode: 'insensitive' };

    const [tenants, total] = await Promise.all([
      prisma.tenant.findMany({
        where,
        skip,
        take,
        orderBy: { createdAt: 'desc' },
        include: { _count: { select: { users: true, professionals: true, appointments: true } } },
      }),
      prisma.tenant.count({ where }),
    ]);

    res.json({
      tenants: tenants.map(t => ({ ...t, _count: undefined, userCount: t._count.users, professionalCount: t._count.professionals, appointmentCount: t._count.appointments })),
      total,
      page: parseInt(page as string),
      totalPages: Math.ceil(total / take),
    });
  } catch (e) { next(e); }
});

adminRouter.get('/users', authenticate, authorize('SUPER_ADMIN'), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { page = '1', limit = '20' } = req.query;
    const users = await prisma.user.findMany({
      skip: (parseInt(page as string) - 1) * parseInt(limit as string),
      take: parseInt(limit as string),
      orderBy: { createdAt: 'desc' },
      include: { tenant: { select: { name: true, slug: true } } },
    });
    const total = await prisma.user.count();
    res.json({ users, total, page: parseInt(page as string), totalPages: Math.ceil(total / parseInt(limit as string)) });
  } catch (e) { next(e); }
});
