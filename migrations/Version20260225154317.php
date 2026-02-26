<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225154317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, total_amount NUMERIC(12, 2) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, currency VARCHAR(3) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, department_id INT NOT NULL, created_by_id INT NOT NULL, INDEX IDX_73F2F77BAE80F5DF (department_id), INDEX IDX_73F2F77BB03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA636ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA679F7D87D FOREIGN KEY (submitted_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77BAE80F5DF');
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77BB03A8386');
        $this->addSql('DROP TABLE budget');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA636ABA6B8');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA679F7D87D');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6FC6B21F1');
    }
}
