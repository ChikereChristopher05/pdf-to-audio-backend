# Base image with PHP + Apache
FROM php:8.2-apache

# Install Python
RUN apt-get update && apt-get install -y python3 python3-pip

# Install required Python packages
COPY requirements.txt /tmp/
RUN pip3 install -r /tmp/requirements.txt

# Enable Apache mod_rewrite (optional, useful for PHP apps)
RUN a2enmod rewrite

# Copy app source code into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 for web traffic
EXPOSE 80

# Default start command (Apache runs automatically in PHP images)
CMD ["apache2-foreground"]
