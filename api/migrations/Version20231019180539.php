<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019180539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment CHANGE is_hiden is_hidden TINYINT(1) NOT NULL DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment CHANGE is_hidden is_hiden TINYINT(1) NOT NULL');
    }
}
