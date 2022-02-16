docker info > /dev/null 2>&1

# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi

# Add .env file from sample
cp .env.example .env

# Build required docker images and run containers from images
docker compose up -d

# Install composer dependencies, generate app key, and migrate database tables
docker compose exec aspire.localhost bash -c "composer install -n && php artisan key:generate && php artisan migrate"