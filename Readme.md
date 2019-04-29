# Netslum: Placeholder

## Create Project

```bash
composer create-project symfony/skeleton .
composer require annotations doctrine twig twig/extensions
composer require --dev profiler phpsu/phpsu
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

## Development

### NPM/Yarn

Build JavaScript & CSS:

```bash
# Production
yarn build

# Development
yarn build:dev
```
