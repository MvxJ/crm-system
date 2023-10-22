<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231022093241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E093133163A');
        $this->addSql('CREATE TABLE customer_address (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, type SMALLINT NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(30) NOT NULL, address VARCHAR(255) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, email_address VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_1193CB3F9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_settings (id INT AUTO_INCREMENT NOT NULL, billing_address_id INT DEFAULT NULL, contact_address_id INT NOT NULL, email_notifications TINYINT(1) NOT NULL, sms_notifications TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C980E3EA79D0C0E4 (billing_address_id), UNIQUE INDEX UNIQ_C980E3EA320EF6E2 (contact_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_settings ADD CONSTRAINT FK_C980E3EA79D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES customer_address (id)');
        $this->addSql('ALTER TABLE customer_settings ADD CONSTRAINT FK_C980E3EA320EF6E2 FOREIGN KEY (contact_address_id) REFERENCES customer_address (id)');
        $this->addSql('ALTER TABLE customer_profile DROP FOREIGN KEY FK_9D8A0EB19395C3F3');
        $this->addSql('DROP TABLE customer_profile');
        $this->addSql('DROP INDEX UNIQ_81398E093133163A ON customer');
        $this->addSql('ALTER TABLE customer ADD first_name VARCHAR(255), ADD second_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255), ADD birth_date DATETIME, ADD social_security_number VARCHAR(13), DROP customer_profile_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customer_profile (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, first_name VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, second_name VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, surname VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, social_security_number VARCHAR(11) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone_number VARCHAR(15) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, birth_date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', UNIQUE INDEX UNIQ_9D8A0EB19395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE customer_profile ADD CONSTRAINT FK_9D8A0EB19395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_address DROP FOREIGN KEY FK_1193CB3F9395C3F3');
        $this->addSql('ALTER TABLE customer_settings DROP FOREIGN KEY FK_C980E3EA79D0C0E4');
        $this->addSql('ALTER TABLE customer_settings DROP FOREIGN KEY FK_C980E3EA320EF6E2');
        $this->addSql('DROP TABLE customer_address');
        $this->addSql('DROP TABLE customer_settings');
        $this->addSql('ALTER TABLE customer ADD customer_profile_id INT DEFAULT NULL, DROP first_name, DROP second_name, DROP last_name, DROP birth_date, DROP social_security_number');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E093133163A FOREIGN KEY (customer_profile_id) REFERENCES customer_profile (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E093133163A ON customer (customer_profile_id)');
    }
}
