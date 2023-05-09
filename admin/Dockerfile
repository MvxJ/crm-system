FROM node:18-slim

WORKDIR /app

ENV PATH ./node_modules/.bin:$PATH

COPY package.json package-lock.json .

RUN npm install

COPY . .

ENTRYPOINT ["npm", "run", "start"]