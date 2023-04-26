symfony server:start
php bin/console doctrine:schema:update --force
php bin/console doctrine:database:create
