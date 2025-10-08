# Projekat - Web Programiranje, Milan Mučić, broj indeksa: 524

- Symfony 7.3  
- PHP 8.2.26  

## Pokretanje projekta

### 1. Instalacija dependencija
```bash
composer install
```

### 2. Podešavanje okruženja
```bash
cp .env .env.local
```

### 3. Kreiranje baze podataka
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
*(Ova komanda učitava početne podatke, uključujući admin nalog.)*
```
php bin/console doctrine:fixtures:load --no-interaction
```

### 4. Pokretanje servera
```bash
php -S 127.0.0.1:8000 -t public
```
