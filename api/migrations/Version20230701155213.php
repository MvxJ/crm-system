<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701155213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(30) NOT NULL, second_name VARCHAR(30) DEFAULT NULL, surname VARCHAR(30) NOT NULL, social_security_number VARCHAR(11) NOT NULL, phone_number VARCHAR(15) NOT NULL, birth_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', UNIQUE INDEX UNIQ_9D8A0EB1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer ADD customer_profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E093133163A FOREIGN KEY (customer_profile_id) REFERENCES customer_profile (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E093133163A ON customer (customer_profile_id)');
        $this->addSql('DROP TABLE IF EXISTS user_profile');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E093133163A');
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB1A76ED395');
        $this->addSql('DROP TABLE customer_profile');
        $this->addSql('DROP INDEX UNIQ_81398E093133163A ON customer');
        $this->addSql('ALTER TABLE customer DROP customer_profile_id');
    }
}
