<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203164932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('ALTER TABLE service_visit ADD color VARCHAR(32) DEFAULT "#91d5ff"');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service_visit DROP color');
    }
}
