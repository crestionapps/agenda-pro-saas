-- Subscription plans
INSERT INTO subscription_plans (name, price, sms_quota, email_quota, whatsapp_quota, is_active, created_at, updated_at) VALUES
('Basic', 19.90, 50, 100, 30, 1, NOW(), NOW()),
('Pro', 49.90, 200, 500, 100, 1, NOW(), NOW()),
('Enterprise', 99.90, 1000, 2000, 500, 1, NOW(), NOW());

-- Tenants (3)
INSERT INTO tenants (name, slug, type, description, address, phone, email, city, is_active, created_at, updated_at) VALUES
('Barbearia Clássica', 'barbearia-classica', 'barbershop', 'Barbearia tradicional com serviços modernos', 'Rua Augusta 24, Lisboa', '+351 210 000 001', 'info@barbeariaclassica.pt', 'Lisboa', 1, NOW(), NOW()),
('SPA Zen Retreat', 'spa-zen-retreat', 'spa', 'Relaxamento e bem-estar no coração da cidade', 'Av. da Liberdade 150, Lisboa', '+351 210 000 002', 'info@spazen.pt', 'Lisboa', 1, NOW(), NOW()),
('Salon Elegance', 'salon-elegance', 'hairdresser', 'Cabeleireiro de luxo com tendências mundiais', 'Rua das Flores 45, Porto', '+351 220 000 003', 'info@salonelegance.pt', 'Porto', 1, NOW(), NOW());

-- Users (password is '123456' hashed with bcrypt)
INSERT INTO users (tenant_id, name, email, password, role, created_at, updated_at) VALUES
(NULL, 'Admin Plataforma', 'admin@plataforma.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NOW(), NOW()),
(1, 'Gerente Barbearia', 'gerente@barbearia-classica.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager', NOW(), NOW()),
(1, 'Cliente Teste', 'cliente@teste.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', NOW(), NOW()),
(1, 'Admin Barbearia', 'admin@barbearia-classica.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- Professionals (3 per tenant)
INSERT INTO professionals (tenant_id, name, email, phone, created_at, updated_at) VALUES
(1, 'Carlos Silva', 'carlos@barbearia.pt', '+351 910 000 001', NOW(), NOW()),
(1, 'Miguel Santos', 'miguel@barbearia.pt', '+351 910 000 002', NOW(), NOW()),
(1, 'João Pereira', 'joao@barbearia.pt', '+351 910 000 003', NOW(), NOW()),
(2, 'Ana Martins', 'ana@spazen.pt', '+351 920 000 001', NOW(), NOW()),
(2, 'Sofia Costa', 'sofia@spazen.pt', '+351 920 000 002', NOW(), NOW()),
(2, 'Rita Oliveira', 'rita@spazen.pt', '+351 920 000 003', NOW(), NOW()),
(3, 'Inês Rodrigues', 'ines@elegance.pt', '+351 930 000 001', NOW(), NOW()),
(3, 'Maria Fernandes', 'maria@elegance.pt', '+351 930 000 002', NOW(), NOW()),
(3, 'Beatriz Almeida', 'beatriz@elegance.pt', '+351 930 000 003', NOW(), NOW());

-- Services
INSERT INTO services (tenant_id, name, price, duration, category, created_at, updated_at) VALUES
(1, 'Corte de Cabelo', 15.00, 30, 'Corte', NOW(), NOW()),
(1, 'Barba Completa', 10.00, 20, 'Barba', NOW(), NOW()),
(1, 'Corte + Barba', 22.00, 45, 'Combo', NOW(), NOW()),
(1, 'Hot Towel Shave', 18.00, 30, 'Barba', NOW(), NOW()),
(2, 'Massagem Relaxante', 45.00, 60, 'Massagem', NOW(), NOW()),
(2, 'Facial Premium', 55.00, 75, 'Facial', NOW(), NOW()),
(2, 'SPA Day', 120.00, 180, 'Tratamento', NOW(), NOW()),
(2, 'Manicure', 30.00, 45, 'Mãos', NOW(), NOW()),
(3, 'Corte e Escovagem', 35.00, 45, 'Corte', NOW(), NOW()),
(3, 'Coloração', 55.00, 90, 'Cor', NOW(), NOW()),
(3, 'Tratamento Capilar', 40.00, 60, 'Tratamento', NOW(), NOW()),
(3, 'Design de Sobrancelhas', 20.00, 30, 'Design', NOW(), NOW());

-- Service-Professional assignments
INSERT INTO service_professional (service_id, professional_id, created_at, updated_at) VALUES
(1,1,NOW(),NOW()),(1,2,NOW(),NOW()),(1,3,NOW(),NOW()),
(2,1,NOW(),NOW()),(2,2,NOW(),NOW()),
(3,1,NOW(),NOW()),(3,2,NOW(),NOW()),
(4,1,NOW(),NOW()),
(5,4,NOW(),NOW()),(5,5,NOW(),NOW()),
(6,4,NOW(),NOW()),(6,6,NOW(),NOW()),
(7,4,NOW(),NOW()),(7,5,NOW(),NOW()),(7,6,NOW(),NOW()),
(8,5,NOW(),NOW()),(8,6,NOW(),NOW()),
(9,7,NOW(),NOW()),(9,8,NOW(),NOW()),
(10,7,NOW(),NOW()),(10,8,NOW(),NOW()),
(11,7,NOW(),NOW()),(11,8,NOW(),NOW()),(11,9,NOW(),NOW()),
(12,7,NOW(),NOW()),(12,9,NOW(),NOW());

-- Business Hours (Mon-Sat 9:00-19:00 for tenant 1 and 3, Mon-Sat 10:00-20:00 for tenant 2)
-- Tenant 1
INSERT INTO business_hours (tenant_id, day_of_week, open_time, close_time, is_open, created_at, updated_at) VALUES
(1,0,NULL,NULL,0,NOW(),NOW()),(1,1,'09:00','19:00',1,NOW(),NOW()),(1,2,'09:00','19:00',1,NOW(),NOW()),(1,3,'09:00','19:00',1,NOW(),NOW()),
(1,4,'09:00','19:00',1,NOW(),NOW()),(1,5,'09:00','19:00',1,NOW(),NOW()),(1,6,'09:00','13:00',1,NOW(),NOW());
-- Tenant 2
INSERT INTO business_hours (tenant_id, day_of_week, open_time, close_time, is_open, created_at, updated_at) VALUES
(2,0,NULL,NULL,0,NOW(),NOW()),(2,1,'10:00','20:00',1,NOW(),NOW()),(2,2,'10:00','20:00',1,NOW(),NOW()),(2,3,'10:00','20:00',1,NOW(),NOW()),
(2,4,'10:00','20:00',1,NOW(),NOW()),(2,5,'10:00','20:00',1,NOW(),NOW()),(2,6,'10:00','18:00',1,NOW(),NOW());
-- Tenant 3
INSERT INTO business_hours (tenant_id, day_of_week, open_time, close_time, is_open, created_at, updated_at) VALUES
(3,0,NULL,NULL,0,NOW(),NOW()),(3,1,'09:00','19:00',1,NOW(),NOW()),(3,2,'09:00','19:00',1,NOW(),NOW()),(3,3,'09:00','19:00',1,NOW(),NOW()),
(3,4,'09:00','19:00',1,NOW(),NOW()),(3,5,'09:00','19:00',1,NOW(),NOW()),(3,6,'09:00','13:00',1,NOW(),NOW());

-- Tenant subscriptions
INSERT INTO tenant_subscriptions (tenant_id, plan_id, start_date, is_active, sms_remaining, email_remaining, whatsapp_remaining, created_at, updated_at) VALUES
(1,1,NOW(),1,50,100,30,NOW(),NOW()),
(2,2,NOW(),1,200,500,100,NOW(),NOW()),
(3,1,NOW(),1,50,100,30,NOW(),NOW());

-- Reviews
INSERT INTO reviews (tenant_id, customer_id, rating, comment, created_at, updated_at) VALUES
(1,3,5,'Excelente serviço! O Carlos é incrível.',NOW(),NOW()),
(1,3,4,'Muito bom, mas tempo de espera um pouco longo.',NOW(),NOW()),
(2,3,5,'Melhor SPA de Lisboa. Super recomendo!',NOW(),NOW()),
(3,3,4,'Adorei o resultado. A Inês é muito talentosa.',NOW(),NOW());
