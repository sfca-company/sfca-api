<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710081217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD logo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF98F144A FOREIGN KEY (logo_id) REFERENCES document (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FF98F144A ON company (logo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF98F144A');
        $this->addSql('DROP INDEX UNIQ_4FBF094FF98F144A ON company');
        $this->addSql('ALTER TABLE company DROP logo_id');
    }
}
