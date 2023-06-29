frontend dockerfile
# Use an official Node.js runtime as the base image
FROM node:16-alpine

# Set the working directory in the container
WORKDIR /app

# Copy package.json and package-lock.json to the working directory
COPY package*.json ./

RUN npm install -g react-scripts@5.0.1

# Install dependencies
# RUN npm install -g create-react-app
# RUN npm install --save react react-dom  
# RUN npm install react-scripts --save

RUN npm install
# RUN npm cache clean --force
# RUN rm -rf node_modules
# RUN npm ci
# --silent
# RUN npm i react-scripts
# Copy the remaining application files to the working directory
COPY . .

# Build the React application
RUN npm run build


# Set the command to run the React application
# CMD ["npm", "start"]
CMD ["npm", "run", "start:dev"]