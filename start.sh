#!/bin/bash

# Ensure runtime directories exist
echo "Setting up directories..."
mkdir -p /var/lib/mysql /run/mysqld
chown -R mysql:mysql /var/lib/mysql /run/mysqld
chmod 700 /var/lib/mysql
chmod 755 /run/mysqld

# Start MySQL service as mysql user with explicit socket
echo "Starting MySQL..."
su - mysql -s /bin/bash -c "/usr/sbin/mysqld --socket=/run/mysqld/mysqld.sock --datadir=/var/lib/mysql --pid-file=/run/mysqld/mysqld.pid &"
sleep 5  # Wait for MySQL to start

# Wait for MySQL to be ready with a 30-second timeout
timeout=30
count=0
until mysqladmin ping -h localhost -u root --socket=/run/mysqld/mysqld.sock --silent; do
    echo "Waiting for MySQL to be ready..."
    sleep 2
    ((count+=2))
    if [ $count -ge $timeout ]; then
        echo "Timeout waiting for MySQL to start. Checking status..."
        ps aux | grep mysql || echo "MySQL process not found."
        cat /var/log/mysql/error.log || echo "No error log available."
        ls -l /run/mysqld/ || echo "Socket directory not accessible."
        exit 1
    fi
done

# Initialize database if not already done
if [ ! -d "/var/lib/mysql/php_docker" ]; then
    echo "Initializing database..."
    mysql -u root --socket=/run/mysqld/mysqld.sock -e "CREATE DATABASE IF NOT EXISTS php_docker;" || { echo "Failed to create database"; exit 1; }
    mysql -u root --socket=/run/mysqld/mysqld.sock -e "CREATE USER IF NOT EXISTS 'php_docker'@'localhost' IDENTIFIED BY 'root';" || { echo "Failed to create user"; exit 1; }
    mysql -u root --socket=/run/mysqld/mysqld.sock -e "GRANT ALL PRIVILEGES ON php_docker.* TO 'php_docker'@'localhost';" || { echo "Failed to grant privileges"; exit 1; }
    mysql -u root --socket=/run/mysqld/mysqld.sock -e "FLUSH PRIVILEGES;" || { echo "Failed to flush privileges"; exit 1; }
    mysql -u root --socket=/run/mysqld/mysqld.sock php_docker < /docker-entrypoint-initdb.d/init.sql || { echo "Failed to load init.sql"; exit 1; }
    echo "Database initialized successfully."
else
    echo "Database already exists, skipping initialization."
fi

echo "MySQL is ready, starting Apache..."
# Start Apache in the foreground
apache2-foreground