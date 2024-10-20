<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020125738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station_user ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_user id_user INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station_user MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON station_user');
        $this->addSql('ALTER TABLE station_user DROP id, CHANGE id_user id_user INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE station_user ADD PRIMARY KEY (id_user)');
    }
}
