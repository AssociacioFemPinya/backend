# Build from Base.Dockerfile
FROM ghcr.io/associaciofempinya/fempinya-base:v1

# Copy existing application directory contents
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Install npm packages
RUN rm -rf node_modules package-lock.json
RUN npm install

# Install application dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port 8000 and start Laravel server
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
