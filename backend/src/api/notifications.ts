import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { ValidationError } from '../lib/errors';

export const notificationsRouter = Router();

const sendSchema = z.object({
  type: z.enum(['SMS', 'EMAIL', 'WHATSAPP']),
  recipient: z.string().min(1),
  message: z.string().min(1),
});

notificationsRouter.post('/send/:tenantId', authenticate, validate(sendSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { type, recipient, message } = req.body;
    const { tenantId } = req.params;

    const sub = await prisma.tenantSubscription.findUnique({
      where: { tenantId },
      include: { plan: true },
    });

    if (!sub || !sub.isActive) {
      throw new ValidationError('Sem subscrição ativa');
    }

    const quotaField = type === 'SMS' ? 'smsRemaining' : type === 'EMAIL' ? 'emailRemaining' : 'whatsappRemaining';
    if ((sub as any)[quotaField] <= 0) {
      throw new ValidationError(`Cota de ${type} esgotada. Contacte o administrador para fazer topup.`);
    }

    await prisma.$transaction(async (tx) => {
      await tx.tenantSubscription.update({
        where: { tenantId },
        data: { [quotaField]: { decrement: 1 } },
      });

      await tx.notificationLog.create({
        data: { tenantId, type, recipient, status: 'SENT' },
      });
    });

    res.json({ success: true, message: `${type} enviado com sucesso para ${recipient}` });
  } catch (e) { next(e); }
});

notificationsRouter.get('/logs/:tenantId', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { page = '1', limit = '20' } = req.query;
    const skip = (parseInt(page as string) - 1) * parseInt(limit as string);
    const take = parseInt(limit as string);

    const [logs, total] = await Promise.all([
      prisma.notificationLog.findMany({
        where: { tenantId: req.params.tenantId },
        orderBy: { createdAt: 'desc' },
        skip,
        take,
      }),
      prisma.notificationLog.count({ where: { tenantId: req.params.tenantId } }),
    ]);

    res.json({ logs, total, page: parseInt(page as string), totalPages: Math.ceil(total / take) });
  } catch (e) { next(e); }
});
