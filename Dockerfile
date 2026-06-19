FROM node:18

WORKDIR /app

# Install backend dependencies
COPY backend/package.json backend/
RUN cd backend && npm install

# Copy all source files
COPY backend/ ./backend/
COPY frontend/ ./frontend/

# Build frontend
RUN cd frontend && npm install && npm run build

# Generate Prisma client and build backend
RUN cd backend && npx prisma generate && npm run build

EXPOSE 3001

CMD ["node", "backend/dist/server.js"]
