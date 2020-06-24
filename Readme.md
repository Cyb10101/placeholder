# Netslum: Placeholder

## Create Project

```bash
composer create-project symfony/skeleton .
composer require annotations doctrine mailer twig twig/extensions
```

### Database

```bash
# Create database
./bin/console doctrine:database:create

# Update schema, get SQL only
./bin/console doctrine:schema:update --dump-sql

# Update schema, force it
./bin/console doctrine:schema:update --force
```

### .env file

```bash
# Docker-Global database
DATABASE_URL=mysql://root:root@global-db/netslum_placeholder

# Docker-Global mail
MAILER_URL=smtp://global-mail:1025
```

## Development

### NPM/Yarn

Build JavaScript & CSS:

```bash
# Production
yarn build

# Development
yarn build:dev
```

### Error pages

Add to url `/_error/number` .

```bash
/_error/404
/_error/500
```
