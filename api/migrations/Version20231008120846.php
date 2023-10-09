<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231008120846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer_model (offer_id INT NOT NULL, model_id INT NOT NULL, INDEX IDX_54B80F3053C674EE (offer_id), INDEX IDX_54B80F307975B7E7 (model_id), PRIMARY KEY(offer_id, model_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer_model ADD CONSTRAINT FK_54B80F3053C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer_model ADD CONSTRAINT FK_54B80F307975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document ADD model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A767975B7E7 FOREIGN KEY (model_id) REFERENCES model (id)');
        $this->addSql('CREATE INDEX IDX_D8698A767975B7E7 ON document (model_id)');
        $this->addSql('ALTER TABLE offer ADD new_users TINYINT(1) NOT NULL, ADD discount DOUBLE PRECISION DEFAULT NULL, ADD type SMALLINT NOT NULL, ADD duration SMALLINT NOT NULL, ADD for_students TINYINT(1) NOT NULL, ADD discount_type SMALLINT NOT NULL, DROP for_new_users, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE percentage_discount number_of_canals INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer_model DROP FOREIGN KEY FK_54B80F3053C674EE');
        $this->addSql('ALTER TABLE offer_model DROP FOREIGN KEY FK_54B80F307975B7E7');
        $this->addSql('DROP TABLE offer_model');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A767975B7E7');
        $this->addSql('DROP INDEX IDX_D8698A767975B7E7 ON document');
        $this->addSql('ALTER TABLE document DROP model_id');
        $this->addSql('ALTER TABLE offer ADD for_new_users TINYINT(1) DEFAULT NULL, DROP new_users, DROP discount, DROP type, DROP duration, DROP for_students, DROP discount_type, CHANGE description description VARCHAR(255) NOT NULL, CHANGE price price INT NOT NULL, CHANGE number_of_canals percentage_discount INT DEFAULT NULL');
    }
}
