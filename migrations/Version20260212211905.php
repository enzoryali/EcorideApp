<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212211905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, commentaire VARCHAR(50) NOT NULL, note VARCHAR(50) NOT NULL, statut VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE covoiturage (id INT AUTO_INCREMENT NOT NULL, date_depart DATE NOT NULL, heure_depart TIME NOT NULL, lieu_depart VARCHAR(255) NOT NULL, date_arrivee DATE NOT NULL, heure_arrivee TIME NOT NULL, lieu_arrivee VARCHAR(255) NOT NULL, statut VARCHAR(50) NOT NULL, nb_place INT NOT NULL, prix_personne DOUBLE PRECISION NOT NULL, duree INT DEFAULT NULL, voiture_id INT NOT NULL, INDEX IDX_28C79E89181A8BA (voiture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parametre (id INT AUTO_INCREMENT NOT NULL, propriete VARCHAR(50) NOT NULL, valeur VARCHAR(50) NOT NULL, configuration_id INT NOT NULL, INDEX IDX_ACC7904173F32DD8 (configuration_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, adresse VARCHAR(50) NOT NULL, date_de_naissance VARCHAR(50) NOT NULL, pseudo VARCHAR(50) NOT NULL, credit INT NOT NULL, photo VARCHAR(255) DEFAULT NULL, is_suspended TINYINT DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_covoiturage (user_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_81DC571CA76ED395 (user_id), INDEX IDX_81DC571C62671590 (covoiturage_id), PRIMARY KEY (user_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_avis (user_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_F510E739A76ED395 (user_id), INDEX IDX_F510E739197E709F (avis_id), PRIMARY KEY (user_id, avis_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, modele VARCHAR(50) NOT NULL, immatriculation VARCHAR(50) NOT NULL, energie VARCHAR(50) NOT NULL, couleur VARCHAR(50) NOT NULL, date_premiere_immatriculation VARCHAR(50) NOT NULL, fumeur TINYINT DEFAULT NULL, animaux TINYINT DEFAULT NULL, preferences_libres VARCHAR(100) DEFAULT NULL, nb_places INT NOT NULL, marque_id INT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_E9E2810F4827B9B2 (marque_id), INDEX IDX_E9E2810FA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE parametre ADD CONSTRAINT FK_ACC7904173F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id)');
        $this->addSql('ALTER TABLE user_covoiturage ADD CONSTRAINT FK_81DC571CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_covoiturage ADD CONSTRAINT FK_81DC571C62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_avis ADD CONSTRAINT FK_F510E739197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA');
        $this->addSql('ALTER TABLE parametre DROP FOREIGN KEY FK_ACC7904173F32DD8');
        $this->addSql('ALTER TABLE user_covoiturage DROP FOREIGN KEY FK_81DC571CA76ED395');
        $this->addSql('ALTER TABLE user_covoiturage DROP FOREIGN KEY FK_81DC571C62671590');
        $this->addSql('ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739A76ED395');
        $this->addSql('ALTER TABLE user_avis DROP FOREIGN KEY FK_F510E739197E709F');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F4827B9B2');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FA76ED395');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE covoiturage');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_covoiturage');
        $this->addSql('DROP TABLE user_avis');
        $this->addSql('DROP TABLE voiture');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
