#!/bin/bash
set -euo pipefail
curl -f http://localhost:${HOST_HTTP_PORT:-8080} || { echo "Health check failed"; exit 1; }
echo "OK"

