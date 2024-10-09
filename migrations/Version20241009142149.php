<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241009142149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE station (id BIGINT NOT NULL, emailuser_id INT DEFAULT NULL, INDEX IDX_9F39F8B123C7E7E6 (emailuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B123C7E7E6 FOREIGN KEY (emailuser_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B123C7E7E6');
        $this->addSql('DROP TABLE station');
        $this->addSql('ALTER TABLE user DROP reset_token, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
