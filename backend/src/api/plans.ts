import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate, authorize } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError, ValidationError } from '../lib/errors';

export const plansRouter = Router();

const createPlanSchema = z.object({
  name: z.string().min(2),
  description: z.string().optional().nullable(),
  price: z.number().min(0),
  smsLimit: z.number().int().min(0).default(0),
  emailLimit: z.number().int().min(0).default(0),
  whatsappLimit: z.number().int().min(0).default(0),
});

const updatePlanSchema = createPlanSchema.partial();

plansRouter.get('/', async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const plans = await prisma.subscriptionPlan.findMany({
      where: { isActive: true },
      orderBy: { price: 'asc' },
    });
    res.json(plans);
  } catch (e) { next(e); }
});

plansRouter.get('/all', authenticate, authorize('SUPER_ADMIN'), async (_req: Request, res: Response, next: NextFunction) => {
  try {
    const plans = await prisma.subscriptionPlan.findMany({ orderBy: { price: 'asc' } });
    res.json(plans);
  } catch (e) { next(e); }
});

plansRouter.post('/', authenticate, authorize('SUPER_ADMIN'), validate(createPlanSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const plan = await prisma.subscriptionPlan.create({ data: req.body });
    res.status(201).json(plan);
  } catch (e) { next(e); }
});

plansRouter.put('/:id', authenticate, authorize('SUPER_ADMIN'), validate(updatePlanSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const plan = await prisma.subscriptionPlan.update({
      where: { id: req.params.id },
      data: req.body,
    });
    res.json(plan);
  } catch (e) { next(e); }
});

plansRouter.delete('/:id', authenticate, authorize('SUPER_ADMIN'), async (req: Request, res: Response, next: NextFunction) => {
  try {
    await prisma.subscriptionPlan.update({
      where: { id: req.params.id },
      data: { isActive: false },
    });
    res.json({ success: true });
  } catch (e) { next(e); }
});

plansRouter.post('/assign/:tenantId', authenticate, authorize('SUPER_ADMIN'), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { planId } = req.body;
    if (!planId) throw new ValidationError('planId é obrigatório');

    const plan = await prisma.subscriptionPlan.findUnique({ where: { id: planId } });
    if (!plan) throw new NotFoundError('Plano');

    const existing = await prisma.tenantSubscription.findUnique({ where: { tenantId: req.params.tenantId } });
    if (existing) {
      await prisma.tenantSubscription.update({
        where: { tenantId: req.params.tenantId },
        data: {
          planId,
          smsRemaining: existing.smsRemaining + plan.smsLimit,
          emailRemaining: existing.emailRemaining + plan.emailLimit,
          whatsappRemaining: existing.whatsappRemaining + plan.whatsappLimit,
          isActive: true,
        },
      });
    } else {
      await prisma.tenantSubscription.create({
        data: {
          tenantId: req.params.tenantId,
          planId,
          smsRemaining: plan.smsLimit,
          emailRemaining: plan.emailLimit,
          whatsappRemaining: plan.whatsappLimit,
        },
      });
    }

    res.json({ success: true });
  } catch (e) { next(e); }
});

plansRouter.get('/tenant/:tenantId', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const sub = await prisma.tenantSubscription.findUnique({
      where: { tenantId: req.params.tenantId },
      include: { plan: true },
    });
    if (!sub) {
      return res.json({ hasSubscription: false, plan: null, quotas: { sms: 0, email: 0, whatsapp: 0 } });
    }
    res.json({
      hasSubscription: true,
      planId: sub.planId,
      plan: sub.plan,
      startDate: sub.startDate,
      endDate: sub.endDate,
      isActive: sub.isActive,
      quotas: {
        sms: { used: sub.plan.smsLimit - sub.smsRemaining, remaining: sub.smsRemaining, total: sub.plan.smsLimit },
        email: { used: sub.plan.emailLimit - sub.emailRemaining, remaining: sub.emailRemaining, total: sub.plan.emailLimit },
        whatsapp: { used: sub.plan.whatsappLimit - sub.whatsappRemaining, remaining: sub.whatsappRemaining, total: sub.plan.whatsappLimit },
      },
    });
  } catch (e) { next(e); }
});

plansRouter.post('/topup/:tenantId', authenticate, authorize('SUPER_ADMIN'), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { sms, email, whatsapp } = req.body;
    const sub = await prisma.tenantSubscription.findUnique({ where: { tenantId: req.params.tenantId } });
    if (!sub) throw new NotFoundError('Subscrição');

    await prisma.tenantSubscription.update({
      where: { tenantId: req.params.tenantId },
      data: {
        smsRemaining: sub.smsRemaining + (sms || 0),
        emailRemaining: sub.emailRemaining + (email || 0),
        whatsappRemaining: sub.whatsappRemaining + (whatsapp || 0),
      },
    });

    res.json({ success: true });
  } catch (e) { next(e); }
});
