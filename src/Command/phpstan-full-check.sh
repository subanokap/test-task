#!/bin/bash

set -e

echo "Starting PHPStan analysis..."

vendor/bin/phpstan analyse src tests --memory-limit=1G

echo "PHPStan finished successfully!"