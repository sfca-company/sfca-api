<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705162916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD address_id INT DEFAULT NULL, ADD phone_number_favorite_id INT DEFAULT NULL, ADD link_societe VARCHAR(255) DEFAULT NULL, ADD siret VARCHAR(14) DEFAULT NULL, ADD city_siret VARCHAR(255) DEFAULT NULL, ADD orias VARCHAR(255) DEFAULT NULL, ADD web_site VARCHAR(255) DEFAULT NULL, ADD currency VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FDD269CE2 FOREIGN KEY (phone_number_favorite_id) REFERENCES phone_number (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FF5B7AF75 ON company (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FDD269CE2 ON company (phone_number_favorite_id)');
        $this->addSql('ALTER TABLE phone_number ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_number ADD CONSTRAINT FK_6B01BC5B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_6B01BC5B979B1AD6 ON phone_number (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF5B7AF75');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FDD269CE2');
        $this->addSql('DROP INDEX UNIQ_4FBF094FF5B7AF75 ON company');
        $this->addSql('DROP INDEX UNIQ_4FBF094FDD269CE2 ON company');
        $this->addSql('ALTER TABLE company DROP address_id, DROP phone_number_favorite_id, DROP link_societe, DROP siret, DROP city_siret, DROP orias, DROP web_site, DROP currency');
        $this->addSql('ALTER TABLE phone_number DROP FOREIGN KEY FK_6B01BC5B979B1AD6');
        $this->addSql('DROP INDEX IDX_6B01BC5B979B1AD6 ON phone_number');
        $this->addSql('ALTER TABLE phone_number DROP company_id');
    }
}
