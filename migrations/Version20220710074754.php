<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710074754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD company_favorite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B3C6F085 FOREIGN KEY (company_favorite_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B3C6F085 ON user (company_favorite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B3C6F085');
        $this->addSql('DROP INDEX IDX_8D93D649B3C6F085 ON user');
        $this->addSql('ALTER TABLE user DROP company_favorite_id');
    }
}
