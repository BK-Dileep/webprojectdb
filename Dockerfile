# Base image with PHP and Apache
FROM php:8.2-apache

# Install MySQL server, client, and PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    default-mysql-client \
    libmariadb-dev \
    && docker-php-ext-install mysqli pdo_mysql \
    && docker-php-ext-enable mysqli pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set index.html as the default page
RUN echo "DirectoryIndex index.html" >> /etc/apache2/apache2.conf

# Copy application files (HTML, CSS, PHP, JPG)
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set up and initialize MySQL data directory and socket directory
RUN mkdir -p /var/lib/mysql && \
    chown -R mysql:mysql /var/lib/mysql && \
    chmod 700 /var/lib/mysql && \
    mkdir -p /run/mysqld && \
    chown -R mysql:mysql /run/mysqld && \
    chmod 755 /run/mysqld

# Copy and prepare init.sql for MySQL initialization
COPY db/init.sql /docker-entrypoint-initdb.d/init.sql

# Copy and configure startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start everything with the script
CMD ["/usr/local/bin/start.sh"]