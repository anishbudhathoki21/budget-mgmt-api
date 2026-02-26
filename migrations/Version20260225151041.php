<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225151041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, total_amount NUMERIC(12, 2) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, currency VARCHAR(3) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, department_id INT NOT NULL, created_by_id INT NOT NULL, INDEX IDX_73F2F77BAE80F5DF (department_id), INDEX IDX_73F2F77BB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(12, 2) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(50) NOT NULL, receipt_note LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, review_comment LONGTEXT DEFAULT NULL, submitted_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, budget_id INT NOT NULL, submitted_by_id INT NOT NULL, reviewed_by_id INT DEFAULT NULL, INDEX IDX_2D3A8DA636ABA6B8 (budget_id), INDEX IDX_2D3A8DA679F7D87D (submitted_by_id), INDEX IDX_2D3A8DA6FC6B21F1 (reviewed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA636ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA679F7D87D FOREIGN KEY (submitted_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649AE80F5DF ON user (department_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77BAE80F5DF');
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77BB03A8386');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA636ABA6B8');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA679F7D87D');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6FC6B21F1');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE expense');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AE80F5DF');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649AE80F5DF ON user');
        $this->addSql('ALTER TABLE user DROP department_id');
    }
}
