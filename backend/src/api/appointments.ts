import { Router, Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { prisma } from '../lib/prisma';
import { authenticate, authorizeTenantAccess } from '../middleware/auth';
import { validate } from '../middleware/validate';
import { NotFoundError, ConflictError, ValidationError } from '../lib/errors';

export const appointmentsRouter = Router();

const createAppointmentSchema = z.object({
  tenantId: z.string(),
  professionalId: z.string(),
  serviceId: z.string(),
  date: z.string().refine(v => !isNaN(Date.parse(v)), 'Data inválida'),
  startTime: z.string().regex(/^\d{2}:\d{2}$/, 'Formato HH:MM'),
  notes: z.string().optional().nullable(),
});

appointmentsRouter.post('/', authenticate, validate(createAppointmentSchema), async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { tenantId, professionalId, serviceId, date, startTime, notes } = req.body;
    const customerId = req.user!.userId;

    const service = await prisma.service.findUnique({ where: { id: serviceId } });
    if (!service) throw new NotFoundError('Serviço');
    if (!service.isActive) throw new ValidationError('Serviço indisponível');

    const professional = await prisma.professional.findUnique({ where: { id: professionalId } });
    if (!professional || !professional.isActive) throw new NotFoundError('Profissional');

    const appointmentDate = new Date(date);
    const dayOfWeek = appointmentDate.getDay();

    const availability = await prisma.availability.findUnique({
      where: { professionalId_dayOfWeek: { professionalId, dayOfWeek } },
    });
    if (!availability) throw new ValidationError('Profissional não disponível neste dia');

    if (startTime < availability.startTime || !timeIsBefore(startTime, availability.endTime, service.duration)) {
      throw new ValidationError('Horário fora da disponibilidade do profissional');
    }

    const endTime = addMinutes(startTime, service.duration);

    const overlapping = await prisma.appointment.findFirst({
      where: {
        professionalId,
        date: appointmentDate,
        status: { notIn: ['CANCELLED'] },
        AND: [
          { startTime: { lt: endTime } },
          { endTime: { gt: startTime } },
        ],
      },
    });

    if (overlapping) {
      throw new ConflictError('Já existe um agendamento neste horário com este profissional');
    }

    const appointment = await prisma.appointment.create({
      data: {
        tenantId,
        customerId,
        professionalId,
        serviceId,
        date: appointmentDate,
        startTime,
        endTime,
        notes,
      },
      include: {
        professional: true,
        service: true,
        customer: { select: { id: true, name: true, email: true, phone: true } },
      },
    });

    res.status(201).json(appointment);
  } catch (e) { next(e); }
});

appointmentsRouter.get('/my', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { status, page = '1', limit = '20' } = req.query;
    const where: any = { customerId: req.user!.userId };
    if (status) where.status = status;

    const appointments = await prisma.appointment.findMany({
      where,
      include: {
        tenant: { select: { id: true, name: true, slug: true, logo: true } },
        professional: { select: { id: true, name: true, photo: true } },
        service: { select: { id: true, name: true, duration: true, price: true } },
      },
      orderBy: { date: 'desc' },
      skip: (parseInt(page as string) - 1) * parseInt(limit as string),
      take: parseInt(limit as string),
    });
    res.json(appointments);
  } catch (e) { next(e); }
});

appointmentsRouter.get('/tenant/:tenantId', authenticate, authorizeTenantAccess, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { status, date, professionalId, page = '1', limit = '50' } = req.query;
    const where: any = { tenantId: req.params.tenantId };
    if (status) where.status = status;
    if (date) where.date = new Date(date as string);
    if (professionalId) where.professionalId = professionalId;

    const appointments = await prisma.appointment.findMany({
      where,
      include: {
        customer: { select: { id: true, name: true, email: true, phone: true } },
        professional: { select: { id: true, name: true, photo: true } },
        service: { select: { id: true, name: true, duration: true, price: true } },
      },
      orderBy: [{ date: 'asc' }, { startTime: 'asc' }],
      skip: (parseInt(page as string) - 1) * parseInt(limit as string),
      take: parseInt(limit as string),
    });
    res.json(appointments);
  } catch (e) { next(e); }
});

appointmentsRouter.get('/availability', async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { professionalId, date } = req.query;
    if (!professionalId || !date) throw new ValidationError('professionalId e date são obrigatórios');

    const appointmentDate = new Date(date as string);
    const dayOfWeek = appointmentDate.getDay();

    const availability = await prisma.availability.findUnique({
      where: { professionalId_dayOfWeek: { professionalId: professionalId as string, dayOfWeek } },
    });
    if (!availability) return res.json({ slots: [] });

    const services = await prisma.service.findMany({
      where: { tenant: { professionals: { some: { id: professionalId as string } } }, isActive: true },
    });

    const appointments = await prisma.appointment.findMany({
      where: {
        professionalId: professionalId as string,
        date: appointmentDate,
        status: { notIn: ['CANCELLED'] },
      },
      orderBy: { startTime: 'asc' },
    });

    const slots = generateAvailableSlots(availability, appointments, services);
    res.json({ slots, date: appointmentDate, dayOfWeek });
  } catch (e) { next(e); }
});

appointmentsRouter.put('/:id/status', authenticate, async (req: Request, res: Response, next: NextFunction) => {
  try {
    const { status } = req.body;
    const validStatuses = ['CONFIRMED', 'CANCELLED', 'COMPLETED', 'NO_SHOW'];
    if (!validStatuses.includes(status)) throw new ValidationError('Status inválido');

    const data: any = { status };
    if (status === 'CANCELLED') {
      data.cancelledAt = new Date();
      data.cancelReason = req.body.reason || null;
    }

    const appointment = await prisma.appointment.update({
      where: { id: req.params.id },
      data,
      include: {
        customer: { select: { id: true, name: true, email: true, phone: true } },
        professional: { select: { id: true, name: true } },
        service: true,
      },
    });
    res.json(appointment);
  } catch (e) { next(e); }
});

function timeIsBefore(time: string, maxTime: string, durationMinutes: number): boolean {
  const [h, m] = time.split(':').map(Number);
  const [mh, mm] = maxTime.split(':').map(Number);
  const endMinutes = h * 60 + m + durationMinutes;
  const maxMinutes = mh * 60 + mm;
  return endMinutes <= maxMinutes;
}

function addMinutes(time: string, minutes: number): string {
  const [h, m] = time.split(':').map(Number);
  const total = h * 60 + m + minutes;
  const nh = Math.floor(total / 60);
  const nm = total % 60;
  return `${String(nh).padStart(2, '0')}:${String(nm).padStart(2, '0')}`;
}

function generateAvailableSlots(
  availability: { startTime: string; endTime: string },
  appointments: { startTime: string; endTime: string }[],
  services: { id: string; duration: number }[]
): { time: string; serviceId?: string }[] {
  const slots: { time: string; serviceId?: string }[] = [];
  const [startH, startM] = availability.startTime.split(':').map(Number);
  const [endH, endM] = availability.endTime.split(':').map(Number);

  const startMinutes = startH * 60 + startM;
  const endMinutes = endH * 60 + endM;

  const busySlots = appointments.map(a => ({
    start: a.startTime,
    end: a.endTime,
  }));

  const minDuration = Math.min(...services.map(s => s.duration));

  for (let m = startMinutes; m + minDuration <= endMinutes; m += 15) {
    const time = `${String(Math.floor(m / 60)).padStart(2, '0')}:${String(m % 60).padStart(2, '0')}`;

    const isBusy = busySlots.some(b => time >= b.start && time < b.end);
    if (isBusy) continue;

    slots.push({ time });
  }

  return slots;
}

export { addMinutes };
