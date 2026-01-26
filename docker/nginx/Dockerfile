FROM nginx:1.17.8-alpine

# Copy the public directory
# COPY .docker /public/ /app/public/
COPY . /app/
#COPY ./src /app
#WORKDIR /app/src


# Copy the nginx config file
COPY nginx.conf /etc/nginx/conf.d/default.conf