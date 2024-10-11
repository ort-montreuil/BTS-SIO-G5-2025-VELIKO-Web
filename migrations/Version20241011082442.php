<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241011082442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B123C7E7E6');
        $this->addSql('DROP INDEX IDX_9F39F8B123C7E7E6 ON station');
        $this->addSql('DROP INDEX `primary` ON station');
        $this->addSql('ALTER TABLE station ADD station_code INT NOT NULL, ADD name VARCHAR(150) NOT NULL, ADD lat DOUBLE PRECISION NOT NULL, ADD lon DOUBLE PRECISION NOT NULL, ADD capacity INT NOT NULL, DROP emailuser_id, CHANGE id station_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE station ADD PRIMARY KEY (station_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON station');
        $this->addSql('ALTER TABLE station ADD emailuser_id INT DEFAULT NULL, DROP station_code, DROP name, DROP lat, DROP lon, DROP capacity, CHANGE station_id id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B123C7E7E6 FOREIGN KEY (emailuser_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9F39F8B123C7E7E6 ON station (emailuser_id)');
        $this->addSql('ALTER TABLE station ADD PRIMARY KEY (id)');
    }
}
