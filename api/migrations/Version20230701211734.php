<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701211734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB19395C3F3');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB19395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB19395C3F3');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB19395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
    }
}
