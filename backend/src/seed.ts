import { PrismaClient, TenantType, UserRole, AppointmentStatus } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  const password = await bcrypt.hash('123456', 10);

  const admin = await prisma.user.upsert({
    where: { email: 'admin@plataforma.pt' },
    update: {},
    create: { name: 'Admin Plataforma', email: 'admin@plataforma.pt', password, role: 'SUPER_ADMIN' },
  });
  console.log('Super admin:', admin.email);

  const tenant = await prisma.tenant.upsert({
    where: { slug: 'barbearia-classica' },
    update: {},
    create: {
      name: 'Barbearia Clássica',
      slug: 'barbearia-classica',
      type: 'BARBERSHOP',
      description: 'A melhor barbearia da cidade com profissionais experientes',
      address: 'Rua Principal, 123, Lisboa',
      phone: '912345678',
      email: 'info@barbeariaclassica.pt',
    },
  });

  const tenant2 = await prisma.tenant.upsert({
    where: { slug: 'spa-bem-estar' },
    update: {},
    create: {
      name: 'Spa Bem-Estar',
      slug: 'spa-bem-estar',
      type: 'SPA',
      description: 'Relaxamento e cuidados pessoais num ambiente único',
      address: 'Av. da Liberdade, 456, Lisboa',
      phone: '913456789',
      email: 'info@spabemestar.pt',
    },
  });

  const tenant3 = await prisma.tenant.upsert({
    where: { slug: 'salon-charme' },
    update: {},
    create: {
      name: 'Salon Charme',
      slug: 'salon-charme',
      type: 'HAIRDRESSER',
      description: 'Cabelo e estética com as melhores tendências',
      address: 'Rua das Flores, 789, Porto',
      phone: '914567890',
      email: 'info@saloncharme.pt',
    },
  });

  await prisma.tenant.upsert({
    where: { slug: 'nail-art-studio' },
    update: {},
    create: {
      name: 'Nail Art Studio',
      slug: 'nail-art-studio',
      type: 'NAILS',
      description: 'Unhas de sonho com as artistas mais talentosas',
      address: 'Rua da Boavista, 321, Porto',
      phone: '915678901',
      email: 'info@nailartstudio.pt',
    },
  });

  await prisma.tenant.upsert({
    where: { slug: 'clinica-dermacare' },
    update: {},
    create: {
      name: 'Clínica Dermacare',
      slug: 'clinica-dermacare',
      type: 'CLINIC',
      description: 'Cuidados dermatológicos e estética avançada',
      address: 'Av. da República, 654, Lisboa',
      phone: '916789012',
      email: 'info@clinicadermacare.pt',
    },
  });

  for (const tenant of [tenant, tenant2, tenant3]) {
    const manager = await prisma.user.upsert({
      where: { email: `gerente@${tenant.slug}.pt` },
      update: {},
      create: { name: `Gerente ${tenant.name}`, email: `gerente@${tenant.slug}.pt`, password, role: 'MANAGER', tenantId: tenant.id },
    });
    console.log(`Manager ${tenant.slug}:`, manager.email);

    for (let i = 0; i < 3; i++) {
      const prof = await prisma.professional.create({
        data: {
          name: `Profissional ${i + 1} - ${tenant.name}`,
          email: `prof${i + 1}@${tenant.slug}.pt`,
          phone: `91${1000000 + i}00000`,
          bio: `Especialista com 5+ anos de experiência`,
          specialties: tenant.type === 'BARBERSHOP' ? ['Corte', 'Barba'] : tenant.type === 'SPA' ? ['Massagem', 'Facial'] : ['Corte', 'Coloração'],
          tenantId: tenant.id,
        },
      });

      for (let day = 1; day <= 5; day++) {
        await prisma.availability.create({
          data: { professionalId: prof.id, dayOfWeek: day, startTime: '09:00', endTime: '18:00' },
        });
      }
    }

    for (let day = 1; day <= 6; day++) {
      await prisma.businessHour.create({
        data: { tenantId: tenant.id, dayOfWeek: day, openTime: '09:00', closeTime: '19:00', isOpen: day <= 5 },
      });
    }

    const getServicesByType = (type: string) => {
      const base = [
        { name: 'Corte de Cabelo', category: 'Corte', duration: 30, price: 15 },
        { name: 'Barba Tradicional', category: 'Barba', duration: 20, price: 10 },
        { name: 'Corte + Barba', category: 'Combo', duration: 45, price: 22 },
        { name: 'Massagem Relaxante', category: 'Massagem', duration: 60, price: 40 },
        { name: 'Tratamento Facial', category: 'Facial', duration: 45, price: 35 },
        { name: 'Manicure', category: 'Mãos', duration: 30, price: 20 },
        { name: 'Pedicure', category: 'Pés', duration: 40, price: 25 },
        { name: 'Coloração', category: 'Cor', duration: 90, price: 50 },
        { name: 'Hidratação Capilar', category: 'Tratamento', duration: 60, price: 30 },
        { name: 'Sobrancelhas', category: 'Design', duration: 15, price: 8 },
      ];
      if (type === 'BARBERSHOP') return base.filter(s => ['Corte', 'Barba', 'Combo'].includes(s.category));
      if (type === 'HAIRDRESSER') return base.filter(s => ['Corte', 'Cor', 'Tratamento', 'Combo'].includes(s.category));
      if (type === 'SPA') return base.filter(s => ['Massagem', 'Facial', 'Mãos', 'Pés'].includes(s.category));
      if (type === 'NAILS') return base.filter(s => ['Mãos', 'Pés', 'Design'].includes(s.category));
      if (type === 'AESTHETICS') return base.filter(s => ['Facial', 'Massagem', 'Design'].includes(s.category));
      return base;
    };

    const tenantServices = getServicesByType(tenant.type);
    const profs = await prisma.professional.findMany({ where: { tenantId: tenant.id } });

    for (const svc of tenantServices) {
      const created = await prisma.service.create({ data: { ...svc, tenantId: tenant.id } });
      for (const prof of profs) {
        await prisma.serviceProfessional.create({
          data: { serviceId: created.id, professionalId: prof.id },
        });
      }
    }
  }

  const freePlan = await prisma.subscriptionPlan.upsert({
    where: { id: 'free-plan' },
    update: {},
    create: {
      id: 'free-plan',
      name: 'Grátis',
      description: 'Para começar. SMS e Email limitados.',
      price: 0,
      smsLimit: 10,
      emailLimit: 50,
      whatsappLimit: 0,
    },
  });

  const basicPlan = await prisma.subscriptionPlan.upsert({
    where: { id: 'basic-plan' },
    update: {},
    create: {
      id: 'basic-plan',
      name: 'Básico',
      description: 'Para negócios em crescimento. 100 SMS, 200 Email, 50 WhatsApp.',
      price: 19.90,
      smsLimit: 100,
      emailLimit: 200,
      whatsappLimit: 50,
    },
  });

  await prisma.subscriptionPlan.upsert({
    where: { id: 'pro-plan' },
    update: {},
    create: {
      id: 'pro-plan',
      name: 'Profissional',
      description: 'Ilimitado. SMS, Email e WhatsApp sem limites.',
      price: 49.90,
      smsLimit: 1000,
      emailLimit: 5000,
      whatsappLimit: 1000,
    },
  });

  console.log('Planos criados');

  for (const tenant of [tenant, tenant2, tenant3]) {
    await prisma.tenantSubscription.upsert({
      where: { tenantId: tenant.id },
      update: {},
      create: {
        tenantId: tenant.id,
        planId: basicPlan.id,
        smsRemaining: basicPlan.smsLimit,
        emailRemaining: basicPlan.emailLimit,
        whatsappRemaining: basicPlan.whatsappLimit,
      },
    });
    console.log(`Subscrição: ${tenant.slug} -> ${basicPlan.name}`);
  }

  const client = await prisma.user.upsert({
    where: { email: 'cliente@teste.pt' },
    update: {},
    create: { name: 'Cliente Teste', email: 'cliente@teste.pt', password, phone: '910000000' },
  });
  console.log('Cliente:', client.email);
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect());
