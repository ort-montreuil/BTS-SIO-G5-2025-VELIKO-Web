<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224211607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, email_user VARCHAR(255) NOT NULL, date DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, id_station_depart BIGINT NOT NULL, id_station_arrivee BIGINT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE station (station_id BIGINT NOT NULL, station_code INT NOT NULL, name VARCHAR(150) NOT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, capacity INT NOT NULL, PRIMARY KEY(station_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE station_user (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_station BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(70) NOT NULL, prenom VARCHAR(102) NOT NULL, date_naissance DATETIME NOT NULL, adresse VARCHAR(45) NOT NULL, code_postal VARCHAR(5) NOT NULL, last_password_changed DATETIME NOT NULL, ville VARCHAR(38) NOT NULL, verified TINYINT(1) NOT NULL, token VARCHAR(255) DEFAULT NULL, is_blocked TINYINT(1) NOT NULL, must_change_password TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE station_user');
        $this->addSql('DROP TABLE user');
    }
}
