<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207102232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('SET foreign_key_checks = 0');
        $this->addSql('ALTER TABLE bill CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE contract_id contract_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE bill_position CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE bill_id bill_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE comment CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_request_id service_request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE is_hidden is_hidden TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE contract CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE offer_id offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE settings_id settings_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE first_name first_name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE birth_date birth_date DATETIME NOT NULL, CHANGE social_security_number social_security_number VARCHAR(13) NOT NULL, CHANGE is_disabled is_disabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE customer_role CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE role_id role_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer_address CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer_settings CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE billing_address_id billing_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE contact_address_id contact_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE device CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE model_id model_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE document CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE author_id author_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE editor_id editor_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE model_id model_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE message CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_request_id service_request_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE model CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notification CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_request_id service_request_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE offer CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE deleted deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE offer_model CHANGE offer_id offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE model_id model_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE bill_id bill_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE role CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE service_request CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE contract_id contract_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE service_visit CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE service_request_id service_request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE customer_id customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE cancelled cancelled TINYINT(1) NOT NULL, CHANGE color color VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE is_deleted is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_role CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE role_id role_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('SET foreign_key_checks = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET foreign_key_checks = 0');        
        $this->addSql('ALTER TABLE bill CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE customer_id customer_id INT NOT NULL, CHANGE contract_id contract_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bill_position CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE bill_id bill_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE service_request_id service_request_id INT NOT NULL, CHANGE is_hidden is_hidden TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE contract CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE offer_id offer_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE settings_id settings_id INT DEFAULT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE birth_date birth_date DATETIME DEFAULT NULL, CHANGE social_security_number social_security_number VARCHAR(13) DEFAULT NULL, CHANGE is_disabled is_disabled TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE customer_address CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE customer_id customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_role CHANGE customer_id customer_id INT NOT NULL, CHANGE role_id role_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer_settings CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE billing_address_id billing_address_id INT DEFAULT NULL, CHANGE contact_address_id contact_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE device CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE model_id model_id INT NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE author_id author_id INT NOT NULL, CHANGE editor_id editor_id INT DEFAULT NULL, CHANGE model_id model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE customer_id customer_id INT NOT NULL, CHANGE service_request_id service_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE model CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE notification CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE service_request_id service_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offer CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE deleted deleted TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE offer_model CHANGE offer_id offer_id INT NOT NULL, CHANGE model_id model_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE bill_id bill_id INT NOT NULL, CHANGE customer_id customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE role CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_request CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE customer_id customer_id INT NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE contract_id contract_id INT DEFAULT NULL, CHANGE status status SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE service_visit CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE service_request_id service_request_id INT NOT NULL, CHANGE customer_id customer_id INT NOT NULL, CHANGE cancelled cancelled TINYINT(1) DEFAULT 0 NOT NULL, CHANGE color color VARCHAR(32) DEFAULT \'#91d5ff\'');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE is_deleted is_deleted TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user_role CHANGE user_id user_id INT NOT NULL, CHANGE role_id role_id INT NOT NULL');
        $this->addql('SET foreign_key_checks = 0');
    }
}
