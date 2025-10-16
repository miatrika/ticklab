#!/bin/bash
set -euo pipefail
# Copy example env if missing and generate key
if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate
fi

