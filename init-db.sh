#!/bin/bash

# Wait for MySQL to be ready
while ! mysqladmin ping -h db -u $MYSQL_USER -p$MYSQL_PASSWORD; do
  echo "Waiting for MySQL to be ready..."
  sleep 5
done

echo "MySQL is ready. Running initialization script..."

mysql -h db -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < /docker-entrypoint-initdb.d/init.sql

echo "Database initialization complete."
