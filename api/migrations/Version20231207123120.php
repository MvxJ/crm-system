<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207123120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bill (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', contract_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', number VARCHAR(255) NOT NULL, date_of_issue DATETIME NOT NULL, payment_date DATETIME DEFAULT NULL, status SMALLINT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, pay_due DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, file_name VARCHAR(255) NOT NULL, INDEX IDX_7A2119E39395C3F3 (customer_id), INDEX IDX_7A2119E32576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_position (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', bill_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', type SMALLINT NOT NULL, price DOUBLE PRECISION NOT NULL, amount INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_CBD02A341A8C12F5 (bill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', service_request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_hidden TINYINT(1) NOT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', contract_number VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, start_date DATETIME NOT NULL, updated_date DATETIME DEFAULT NULL, create_date DATETIME NOT NULL, price DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, discount_type SMALLINT DEFAULT NULL, total_sum DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_E98F2859A76ED395 (user_id), INDEX IDX_E98F285953C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', settings_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_auth_code VARCHAR(255) DEFAULT NULL, authenticated TINYINT(1) NOT NULL, email_auth_enabled TINYINT(1) NOT NULL, first_name VARCHAR(255) NOT NULL, second_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, social_security_number VARCHAR(13) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, is_disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_81398E0959949888 (settings_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_role (customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', role_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_3F82E1D9395C3F3 (customer_id), INDEX IDX_3F82E1DD60322AC (role_id), PRIMARY KEY(customer_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_address (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', type SMALLINT NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(30) NOT NULL, address VARCHAR(255) NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, email_address VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_1193CB3F9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_settings (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', billing_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', contact_address_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', email_notifications TINYINT(1) NOT NULL, sms_notifications TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C980E3EA79D0C0E4 (billing_address_id), UNIQUE INDEX UNIQ_C980E3EA320EF6E2 (contact_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', model_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', serial_number VARCHAR(255) NOT NULL, mac_address VARCHAR(255) NOT NULL, bought_date DATETIME NOT NULL, status SMALLINT NOT NULL, sold_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_92FB68ED948EE2 (serial_number), UNIQUE INDEX UNIQ_92FB68EB728E969 (mac_address), INDEX IDX_92FB68E7975B7E7 (model_id), INDEX IDX_92FB68EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', author_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', editor_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', model_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_for_clients TINYINT(1) NOT NULL, INDEX IDX_D8698A76F675F31B (author_id), INDEX IDX_D8698A766995AC4C (editor_id), INDEX IDX_D8698A767975B7E7 (model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', service_request_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_date DATETIME NOT NULL, message LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, phone_number VARCHAR(30) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, INDEX IDX_B6BD307F9395C3F3 (customer_id), INDEX IDX_B6BD307FD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', manufacturer VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, params LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, price DOUBLE PRECISION NOT NULL, is_deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', service_request_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_date DATETIME NOT NULL, is_readed TINYINT(1) NOT NULL, date_of_read DATETIME NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CAD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, download_speed DOUBLE PRECISION DEFAULT NULL, upload_speed DOUBLE PRECISION DEFAULT NULL, new_users TINYINT(1) NOT NULL, price DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, type SMALLINT NOT NULL, duration SMALLINT NOT NULL, number_of_canals INT DEFAULT NULL, for_students TINYINT(1) NOT NULL, discount_type SMALLINT DEFAULT NULL, valid_due DATETIME DEFAULT NULL, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_model (offer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', model_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_54B80F3053C674EE (offer_id), INDEX IDX_54B80F307975B7E7 (model_id), PRIMARY KEY(offer_id, model_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', bill_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL, paid_by SMALLINT NOT NULL, amount DOUBLE PRECISION NOT NULL, note LONGTEXT DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_6D28840D1A8C12F5 (bill_id), INDEX IDX_6D28840D9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', role VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_request (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', contract_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_date DATETIME NOT NULL, close_date DATETIME DEFAULT NULL, is_closed TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, status SMALLINT NOT NULL, INDEX IDX_F413DD039395C3F3 (customer_id), INDEX IDX_F413DD03A76ED395 (user_id), INDEX IDX_F413DD032576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_visit (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', service_request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_finished TINYINT(1) NOT NULL, cancelled TINYINT(1) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, color VARCHAR(32) DEFAULT NULL, INDEX IDX_EA0C04DEA76ED395 (user_id), INDEX IDX_EA0C04DED42F8111 (service_request_id), INDEX IDX_EA0C04DE9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, company_phone_number VARCHAR(20) DEFAULT NULL, facebook_url VARCHAR(255) DEFAULT NULL, company_address VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, privacy_policy VARCHAR(255) DEFAULT NULL, terms_and_conditions VARCHAR(255) DEFAULT NULL, technical_support_number VARCHAR(20) DEFAULT NULL, email_address VARCHAR(255) DEFAULT NULL, mailer_address VARCHAR(255) DEFAULT NULL, mailer_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, auth_code VARCHAR(255) DEFAULT NULL, email_auth TINYINT(1) NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', role_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2DE8C6A3A76ED395 (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E32576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE bill_position ADD CONSTRAINT FK_CBD02A341A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A76ED395 FOREIGN KEY (user_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285953C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0959949888 FOREIGN KEY (settings_id) REFERENCES customer_settings (id)');
        $this->addSql('ALTER TABLE customer_role ADD CONSTRAINT FK_3F82E1D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_role ADD CONSTRAINT FK_3F82E1DD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_settings ADD CONSTRAINT FK_C980E3EA79D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES customer_address (id)');
        $this->addSql('ALTER TABLE customer_settings ADD CONSTRAINT FK_C980E3EA320EF6E2 FOREIGN KEY (contact_address_id) REFERENCES customer_address (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A766995AC4C FOREIGN KEY (editor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A767975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE offer_model ADD CONSTRAINT FK_54B80F3053C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer_model ADD CONSTRAINT FK_54B80F307975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D1A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD039395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD032576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DED42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E39395C3F3');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E32576E0FD');
        $this->addSql('ALTER TABLE bill_position DROP FOREIGN KEY FK_CBD02A341A8C12F5');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CD42F8111');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859A76ED395');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285953C674EE');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0959949888');
        $this->addSql('ALTER TABLE customer_role DROP FOREIGN KEY FK_3F82E1D9395C3F3');
        $this->addSql('ALTER TABLE customer_role DROP FOREIGN KEY FK_3F82E1DD60322AC');
        $this->addSql('ALTER TABLE customer_address DROP FOREIGN KEY FK_1193CB3F9395C3F3');
        $this->addSql('ALTER TABLE customer_settings DROP FOREIGN KEY FK_C980E3EA79D0C0E4');
        $this->addSql('ALTER TABLE customer_settings DROP FOREIGN KEY FK_C980E3EA320EF6E2');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E7975B7E7');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EA76ED395');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76F675F31B');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A766995AC4C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A767975B7E7');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9395C3F3');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FD42F8111');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAD42F8111');
        $this->addSql('ALTER TABLE offer_model DROP FOREIGN KEY FK_54B80F3053C674EE');
        $this->addSql('ALTER TABLE offer_model DROP FOREIGN KEY FK_54B80F307975B7E7');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D1A8C12F5');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9395C3F3');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD039395C3F3');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD03A76ED395');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD032576E0FD');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DEA76ED395');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DED42F8111');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DE9395C3F3');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3A76ED395');
        $this->addSql('ALTER TABLE user_role DROP FOREIGN KEY FK_2DE8C6A3D60322AC');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE bill_position');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_role');
        $this->addSql('DROP TABLE customer_address');
        $this->addSql('DROP TABLE customer_settings');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_model');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE service_request');
        $this->addSql('DROP TABLE service_visit');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role');
    }
}
