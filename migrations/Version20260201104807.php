<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260201104807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_covoiturage (utilisateur_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_DC21931AFB88E14F (utilisateur_id), INDEX IDX_DC21931A62671590 (covoiturage_id), PRIMARY KEY (utilisateur_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur_role (utilisateur_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_9EE8E650FB88E14F (utilisateur_id), INDEX IDX_9EE8E650D60322AC (role_id), PRIMARY KEY (utilisateur_id, role_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur_avis (utilisateur_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_4610C7CAFB88E14F (utilisateur_id), INDEX IDX_4610C7CA197E709F (avis_id), PRIMARY KEY (utilisateur_id, avis_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE utilisateur_covoiturage ADD CONSTRAINT FK_DC21931AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_covoiturage ADD CONSTRAINT FK_DC21931A62671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_avis ADD CONSTRAINT FK_4610C7CAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_avis ADD CONSTRAINT FK_4610C7CA197E709F FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE depose_avis DROP FOREIGN KEY `FK_D4039209197E709F`');
        $this->addSql('ALTER TABLE depose_avis DROP FOREIGN KEY `FK_D403920941CD8671`');
        $this->addSql('ALTER TABLE depose_utilisateur DROP FOREIGN KEY `FK_7845B60F41CD8671`');
        $this->addSql('ALTER TABLE depose_utilisateur DROP FOREIGN KEY `FK_7845B60FFB88E14F`');
        $this->addSql('ALTER TABLE participe_covoiturage DROP FOREIGN KEY `FK_2F9A18A662671590`');
        $this->addSql('ALTER TABLE participe_covoiturage DROP FOREIGN KEY `FK_2F9A18A6A71D81B9`');
        $this->addSql('ALTER TABLE participe_utilisateur DROP FOREIGN KEY `FK_1A41E59CA71D81B9`');
        $this->addSql('ALTER TABLE participe_utilisateur DROP FOREIGN KEY `FK_1A41E59CFB88E14F`');
        $this->addSql('ALTER TABLE possede_role DROP FOREIGN KEY `FK_3B46B142C835AB29`');
        $this->addSql('ALTER TABLE possede_role DROP FOREIGN KEY `FK_3B46B142D60322AC`');
        $this->addSql('ALTER TABLE possede_utilisateur DROP FOREIGN KEY `FK_BD921348C835AB29`');
        $this->addSql('ALTER TABLE possede_utilisateur DROP FOREIGN KEY `FK_BD921348FB88E14F`');
        $this->addSql('DROP TABLE depose');
        $this->addSql('DROP TABLE depose_avis');
        $this->addSql('DROP TABLE depose_utilisateur');
        $this->addSql('DROP TABLE participe');
        $this->addSql('DROP TABLE participe_covoiturage');
        $this->addSql('DROP TABLE participe_utilisateur');
        $this->addSql('DROP TABLE possede');
        $this->addSql('DROP TABLE possede_role');
        $this->addSql('DROP TABLE possede_utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE depose (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE depose_avis (depose_id INT NOT NULL, avis_id INT NOT NULL, INDEX IDX_D403920941CD8671 (depose_id), INDEX IDX_D4039209197E709F (avis_id), PRIMARY KEY (depose_id, avis_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE depose_utilisateur (depose_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_7845B60FFB88E14F (utilisateur_id), INDEX IDX_7845B60F41CD8671 (depose_id), PRIMARY KEY (depose_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participe (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participe_covoiturage (participe_id INT NOT NULL, covoiturage_id INT NOT NULL, INDEX IDX_2F9A18A6A71D81B9 (participe_id), INDEX IDX_2F9A18A662671590 (covoiturage_id), PRIMARY KEY (participe_id, covoiturage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participe_utilisateur (participe_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_1A41E59CA71D81B9 (participe_id), INDEX IDX_1A41E59CFB88E14F (utilisateur_id), PRIMARY KEY (participe_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE possede (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE possede_role (possede_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_3B46B142C835AB29 (possede_id), INDEX IDX_3B46B142D60322AC (role_id), PRIMARY KEY (possede_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE possede_utilisateur (possede_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_BD921348C835AB29 (possede_id), INDEX IDX_BD921348FB88E14F (utilisateur_id), PRIMARY KEY (possede_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE depose_avis ADD CONSTRAINT `FK_D4039209197E709F` FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE depose_avis ADD CONSTRAINT `FK_D403920941CD8671` FOREIGN KEY (depose_id) REFERENCES depose (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE depose_utilisateur ADD CONSTRAINT `FK_7845B60F41CD8671` FOREIGN KEY (depose_id) REFERENCES depose (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE depose_utilisateur ADD CONSTRAINT `FK_7845B60FFB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participe_covoiturage ADD CONSTRAINT `FK_2F9A18A662671590` FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participe_covoiturage ADD CONSTRAINT `FK_2F9A18A6A71D81B9` FOREIGN KEY (participe_id) REFERENCES participe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participe_utilisateur ADD CONSTRAINT `FK_1A41E59CA71D81B9` FOREIGN KEY (participe_id) REFERENCES participe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participe_utilisateur ADD CONSTRAINT `FK_1A41E59CFB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE possede_role ADD CONSTRAINT `FK_3B46B142C835AB29` FOREIGN KEY (possede_id) REFERENCES possede (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE possede_role ADD CONSTRAINT `FK_3B46B142D60322AC` FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE possede_utilisateur ADD CONSTRAINT `FK_BD921348C835AB29` FOREIGN KEY (possede_id) REFERENCES possede (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE possede_utilisateur ADD CONSTRAINT `FK_BD921348FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP FOREIGN KEY FK_DC21931AFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP FOREIGN KEY FK_DC21931A62671590');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650D60322AC');
        $this->addSql('ALTER TABLE utilisateur_avis DROP FOREIGN KEY FK_4610C7CAFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_avis DROP FOREIGN KEY FK_4610C7CA197E709F');
        $this->addSql('DROP TABLE utilisateur_covoiturage');
        $this->addSql('DROP TABLE utilisateur_role');
        $this->addSql('DROP TABLE utilisateur_avis');
    }
}
