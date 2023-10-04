<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231004165654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE model DROP status');
        $this->addSql('ALTER TABLE service_visit CHANGE is_finished is_finished TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE model ADD status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE service_visit CHANGE is_finished is_finished SMALLINT NOT NULL');
    }
}
