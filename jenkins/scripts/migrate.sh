#!/bin/bash
set -euo pipefail
docker-compose run --rm app php artisan migrate --force

