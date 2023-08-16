FROM node:lts-alpine

WORKDIR /frontend

COPY frontend/package.json /frontend

RUN npm install

COPY frontend /frontend

EXPOSE 5173

CMD ["npm", "run", "dev"]
