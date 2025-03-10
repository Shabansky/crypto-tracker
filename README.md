# Bitcoin Tracker

## Installation

For the below commands, use `docker compose` for newer Docker versions
and `docker-compose` for older ones. The following guide will assume the use
of `docker compose`.

1. Build and boot the project
`docker compose up --build -d`

2. Install dependencies
`docker compose exec api composer install`

## Notable Features

### Subscription API

The system supports an API for viewing and modifiying subscriptions. 


An OpenAPI Documentation of the project can be found at `./openapi.yaml`