<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230630144359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_auth_code VARCHAR(255) DEFAULT NULL, authenticated TINYINT(1) NOT NULL, email_auth_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_role (customer_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_3F82E1D9395C3F3 (customer_id), INDEX IDX_3F82E1DD60322AC (role_id), PRIMARY KEY(customer_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_role ADD CONSTRAINT FK_3F82E1D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_role ADD CONSTRAINT FK_3F82E1DD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) NOT NULL, ADD surname VARCHAR(255) NOT NULL, ADD phone_number VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_role DROP FOREIGN KEY FK_3F82E1D9395C3F3');
        $this->addSql('ALTER TABLE customer_role DROP FOREIGN KEY FK_3F82E1DD60322AC');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_role');
        $this->addSql('ALTER TABLE user DROP name, DROP surname, DROP phone_number');
    }
}
