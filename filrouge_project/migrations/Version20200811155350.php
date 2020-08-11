<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200811155350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupes (id INT AUTO_INCREMENT NOT NULL, promos_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_creation VARCHAR(255) NOT NULL, statut TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_576366D9CAA392D2 (promos_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupes_formateur (groupes_id INT NOT NULL, formateur_id INT NOT NULL, INDEX IDX_9481F39E305371B (groupes_id), INDEX IDX_9481F39E155D8F51 (formateur_id), PRIMARY KEY(groupes_id, formateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupes_apprenant (groupes_id INT NOT NULL, apprenant_id INT NOT NULL, INDEX IDX_BD1CCBFF305371B (groupes_id), INDEX IDX_BD1CCBFFC5697D6D (apprenant_id), PRIMARY KEY(groupes_id, apprenant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promos (id INT AUTO_INCREMENT NOT NULL, referentiel_id INT DEFAULT NULL, langue VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, lieu VARCHAR(255) NOT NULL, reference_agate VARCHAR(255) NOT NULL, date_debut VARCHAR(255) NOT NULL, date_provisoire DATE NOT NULL, date_fin DATE NOT NULL, fabrique VARCHAR(255) NOT NULL, INDEX IDX_31D1F705805DB139 (referentiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promos_formateur (promos_id INT NOT NULL, formateur_id INT NOT NULL, INDEX IDX_70F76221CAA392D2 (promos_id), INDEX IDX_70F76221155D8F51 (formateur_id), PRIMARY KEY(promos_id, formateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE groupes ADD CONSTRAINT FK_576366D9CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id)');
        $this->addSql('ALTER TABLE groupes_formateur ADD CONSTRAINT FK_9481F39E305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_formateur ADD CONSTRAINT FK_9481F39E155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFF305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFFC5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F705805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id)');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGBLOB DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupes_formateur DROP FOREIGN KEY FK_9481F39E305371B');
        $this->addSql('ALTER TABLE groupes_apprenant DROP FOREIGN KEY FK_BD1CCBFF305371B');
        $this->addSql('ALTER TABLE groupes DROP FOREIGN KEY FK_576366D9CAA392D2');
        $this->addSql('ALTER TABLE promos_formateur DROP FOREIGN KEY FK_70F76221CAA392D2');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('DROP TABLE groupes_formateur');
        $this->addSql('DROP TABLE groupes_apprenant');
        $this->addSql('DROP TABLE promos');
        $this->addSql('DROP TABLE promos_formateur');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
