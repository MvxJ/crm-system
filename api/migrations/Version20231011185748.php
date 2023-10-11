<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011185748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bill (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, contract_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, date_of_issue DATETIME NOT NULL, payment_date DATETIME DEFAULT NULL, status SMALLINT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, pay_due DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, INDEX IDX_7A2119E39395C3F3 (customer_id), INDEX IDX_7A2119E32576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bill_position (id INT AUTO_INCREMENT NOT NULL, bill_id INT NOT NULL, type SMALLINT NOT NULL, `float` DOUBLE PRECISION NOT NULL, `integer` INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_CBD02A341A8C12F5 (bill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, offer_id INT NOT NULL, contract_number VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, start_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, create_date DATETIME NOT NULL, price DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, discount_type SMALLINT DEFAULT NULL, total_sum DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_E98F2859A76ED395 (user_id), INDEX IDX_E98F285953C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, bill_id INT NOT NULL, customer_id INT NOT NULL, created_at DATETIME NOT NULL, paid_by SMALLINT NOT NULL, amount DOUBLE PRECISION NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_6D28840D1A8C12F5 (bill_id), INDEX IDX_6D28840D9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E32576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE bill_position ADD CONSTRAINT FK_CBD02A341A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A76ED395 FOREIGN KEY (user_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285953C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D1A8C12F5 FOREIGN KEY (bill_id) REFERENCES bill (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE service_request ADD contract_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD032576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('CREATE INDEX IDX_F413DD032576E0FD ON service_request (contract_id)');
        $this->addSql('ALTER TABLE service_visit ADD customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_EA0C04DE9395C3F3 ON service_visit (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD032576E0FD');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E39395C3F3');
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E32576E0FD');
        $this->addSql('ALTER TABLE bill_position DROP FOREIGN KEY FK_CBD02A341A8C12F5');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859A76ED395');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285953C674EE');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D1A8C12F5');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9395C3F3');
        $this->addSql('DROP TABLE bill');
        $this->addSql('DROP TABLE bill_position');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP INDEX IDX_F413DD032576E0FD ON service_request');
        $this->addSql('ALTER TABLE service_request DROP contract_id');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DE9395C3F3');
        $this->addSql('DROP INDEX IDX_EA0C04DE9395C3F3 ON service_visit');
        $this->addSql('ALTER TABLE service_visit DROP customer_id');
    }
}
