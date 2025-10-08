Projekat – Web Programiranje

Tehnologije:

Symfony 7.3

PHP 8.2.26

Pokretanje projekta
1. Instalacija dependencija
composer install

2. Podešavanje okruženja
cp .env .env.local

3. Kreiranje baze podataka
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load --no-interaction
(Ova komanda učitava početne podatke, uključujući admin nalog.)

5. Pokretanje servera
php -S 127.0.0.1:8000 -t public
