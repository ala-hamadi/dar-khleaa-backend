<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230428220319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP FOREIGN KEY FK_4D7E68549D86650F');
        $this->addSql('DROP INDEX IDX_4D7E68549D86650F ON apartment');
        $this->addSql('ALTER TABLE apartment CHANGE user_id_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE apartment ADD CONSTRAINT FK_4D7E6854A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4D7E6854A76ED395 ON apartment (user_id)');
        $this->addSql('ALTER TABLE reservation DROP guest_name, DROP guest_email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP FOREIGN KEY FK_4D7E6854A76ED395');
        $this->addSql('DROP INDEX IDX_4D7E6854A76ED395 ON apartment');
        $this->addSql('ALTER TABLE apartment CHANGE user_id user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE apartment ADD CONSTRAINT FK_4D7E68549D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4D7E68549D86650F ON apartment (user_id_id)');
        $this->addSql('ALTER TABLE reservation ADD guest_name VARCHAR(255) NOT NULL, ADD guest_email VARCHAR(255) NOT NULL');
    }
}
