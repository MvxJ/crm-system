<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006184211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device CHANGE mac_address mac_address VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68ED948EE2 ON device (serial_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68EB728E969 ON device (mac_address)');
        $this->addSql('ALTER TABLE model CHANGE is_deleted is_deleted TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_92FB68ED948EE2 ON device');
        $this->addSql('DROP INDEX UNIQ_92FB68EB728E969 ON device');
        $this->addSql('ALTER TABLE device CHANGE mac_address mac_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE model CHANGE is_deleted is_deleted TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
