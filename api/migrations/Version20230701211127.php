<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701211127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB1A76ED395');
        $this->addSql('DROP INDEX UNIQ_9D8A0EB1A76ED395 ON customer_profile');
        $this->addSql('ALTER TABLE customer_profile CHANGE user_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB19395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D8A0EB19395C3F3 ON customer_profile (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB19395C3F3');
        $this->addSql('DROP INDEX UNIQ_9D8A0EB19395C3F3 ON customer_profile');
        $this->addSql('ALTER TABLE customer_profile CHANGE customer_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D8A0EB1A76ED395 ON customer_profile (user_id)');
    }
}
