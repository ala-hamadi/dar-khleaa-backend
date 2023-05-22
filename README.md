Start project: symfony server:start
nbdel fi entity: php bin/console doctrine:schema:update --force
create database meloul: php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
https://symfony.com/doc/current/doctrine.html
