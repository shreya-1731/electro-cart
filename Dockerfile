FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy all project files
COPY . /var/www/html/

# 1. REMOVE the root .htaccess file (fixes the 403 Forbidden error)
RUN rm -f /var/www/html/.htaccess

# 2. Automatically recreate folder structure for all files uploaded directly to the root
RUN mkdir -p /var/www/html/config \
             /var/www/html/includes \
             /var/www/html/api \
             /var/www/html/data \
             /var/www/html/assets/css \
             /var/www/html/assets/js \
             /var/www/html/assets/images/products

# Reorganize PHP files into their required directories
RUN [ -f /var/www/html/db.php ] && cp /var/www/html/db.php /var/www/html/config/db.php || true
RUN [ -f /var/www/html/header.php ] && cp /var/www/html/header.php /var/www/html/includes/header.php || true
RUN [ -f /var/www/html/footer.php ] && cp /var/www/html/footer.php /var/www/html/includes/footer.php || true
RUN [ -f /var/www/html/cart_action.php ] && cp /var/www/html/cart_action.php /var/www/html/api/cart_action.php || true
RUN [ -f /var/www/html/electro_cart.db ] && cp /var/www/html/electro_cart.db /var/www/html/data/electro_cart.db || true
RUN [ -f /var/www/html/schema.sql ] && cp /var/www/html/schema.sql /var/www/html/data/schema.sql || true

# Reorganize CSS stylesheets
RUN [ -f /var/www/html/style.css ] && cp /var/www/html/style.css /var/www/html/assets/css/style.css || true
RUN [ -f /var/www/html/animations.css ] && cp /var/www/html/animations.css /var/www/html/assets/css/animations.css || true

# Reorganize JavaScript files
RUN [ -f /var/www/html/main.js ] && cp /var/www/html/main.js /var/www/html/assets/js/main.js || true
RUN [ -f /var/www/html/cart.js ] && cp /var/www/html/cart.js /var/www/html/assets/js/cart.js || true

# Reorganize Images into both assets/images/ AND assets/images/products/
RUN cp /var/www/html/*.jpg /var/www/html/assets/images/ 2>/dev/null || true
RUN cp /var/www/html/*.png /var/www/html/assets/images/ 2>/dev/null || true
RUN cp /var/www/html/*.svg /var/www/html/assets/images/ 2>/dev/null || true
RUN cp /var/www/html/p*.jpg /var/www/html/assets/images/products/ 2>/dev/null || true
RUN cp /var/www/html/assets/images/p*.jpg /var/www/html/assets/images/products/ 2>/dev/null || true

# 3. Set Apache permissions for www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data

# 4. Configure Apache VirtualHost
RUN echo '<VirtualHost *:${PORT}>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php index.html\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 5. Bind Apache to Render dynamic PORT
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf

ENV PORT=10000
EXPOSE 10000

CMD ["apache2-foreground"]
