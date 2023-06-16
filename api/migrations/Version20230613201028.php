<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613201028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, company_phone_number VARCHAR(20) DEFAULT NULL, facebook_url VARCHAR(255), company_address VARCHAR(255), company_name VARCHAR(255), privacy_policy VARCHAR(255) DEFAULT NULL, terms_and_conditions VARCHAR(255) DEFAULT NULL, technical_support_number VARCHAR(20) DEFAULT NULL, email_address VARCHAR(255) DEFAULT NULL, mailer_address VARCHAR(255) DEFAULT NULL, mailer_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE settings');
    }
}
