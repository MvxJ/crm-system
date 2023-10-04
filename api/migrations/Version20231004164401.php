<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231004164401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_request_id INT NOT NULL, created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_hiden TINYINT(1) NOT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, model_id INT NOT NULL, user_id INT DEFAULT NULL, serial_number VARCHAR(255) NOT NULL, mac_address VARCHAR(255) DEFAULT NULL, bought_date DATETIME NOT NULL, status INT NOT NULL, sold_date DATETIME DEFAULT NULL, INDEX IDX_92FB68E7975B7E7 (model_id), INDEX IDX_92FB68EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, editor_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_for_clients TINYINT(1) NOT NULL, INDEX IDX_D8698A76F675F31B (author_id), INDEX IDX_D8698A766995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, service_request_id INT DEFAULT NULL, created_date DATETIME NOT NULL, message LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, INDEX IDX_B6BD307F9395C3F3 (customer_id), INDEX IDX_B6BD307FD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model (id INT AUTO_INCREMENT NOT NULL, manufacturer VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, params LONGTEXT DEFAULT NULL, type SMALLINT NOT NULL, status SMALLINT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_request_id INT DEFAULT NULL, created_date DATETIME NOT NULL, is_readed TINYINT(1) NOT NULL, date_of_read DATETIME NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CAD42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_request (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, user_id INT DEFAULT NULL, created_date DATETIME NOT NULL, close_date DATETIME DEFAULT NULL, is_closed TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_F413DD039395C3F3 (customer_id), INDEX IDX_F413DD03A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_visit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, service_request_id INT NOT NULL, date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_date DATETIME NOT NULL, edit_date DATETIME DEFAULT NULL, is_finished SMALLINT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, INDEX IDX_EA0C04DEA76ED395 (user_id), INDEX IDX_EA0C04DED42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A766995AC4C FOREIGN KEY (editor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD039395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_visit ADD CONSTRAINT FK_EA0C04DED42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CD42F8111');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E7975B7E7');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68EA76ED395');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76F675F31B');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A766995AC4C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9395C3F3');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FD42F8111');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAD42F8111');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD039395C3F3');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD03A76ED395');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DEA76ED395');
        $this->addSql('ALTER TABLE service_visit DROP FOREIGN KEY FK_EA0C04DED42F8111');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE service_request');
        $this->addSql('DROP TABLE service_visit');
    }
}
