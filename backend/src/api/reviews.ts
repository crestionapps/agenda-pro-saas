import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError } from '../lib/errors';

export const reviewsRouter = Router();

const createReviewSchema = z.object({
  tenantId: z.string(),
  rating: z.number().int().min(1).max(5),
  comment: z.string().optional().nullable(),
});

reviewsRouter.post('/', authenticate, validate(createReviewSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const review = await prisma.review.create({
      data: { ...req.body, customerId: req.user!.userId },
    });
    res.status(201).json(review);
  } catch (e) { next(e); }
});

reviewsRouter.get('/tenant/:tenantId', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const reviews = await prisma.review.findMany({
      where: { tenantId: req.params.tenantId },
      include: { customer: { select: { name: true, avatar: true } } },
      orderBy: { createdAt: 'desc' },
    });
    const avg = await prisma.review.aggregate({
      where: { tenantId: req.params.tenantId },
      _avg: { rating: true },
      _count: true,
    });
    res.json({ reviews, average: avg._avg.rating ?? 0, total: avg._count });
  } catch (e) { next(e); }
});

reviewsRouter.put('/:id/reply', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { managerReply } = req.body;
    const review = await prisma.review.update({
      where: { id: req.params.id },
      data: { managerReply },
    });
    res.json(review);
  } catch (e) { next(e); }
});
