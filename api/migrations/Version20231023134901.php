<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023134901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD settings_id INT');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0959949888 FOREIGN KEY (settings_id) REFERENCES customer_settings (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E0959949888 ON customer (settings_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0959949888');
        $this->addSql('DROP INDEX UNIQ_81398E0959949888 ON customer');
        $this->addSql('ALTER TABLE customer DROP settings_id');
    }
}
