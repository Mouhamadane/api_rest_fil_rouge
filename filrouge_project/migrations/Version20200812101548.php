<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200812101548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, is_deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence_groupe_competence (competence_id INT NOT NULL, groupe_competence_id INT NOT NULL, INDEX IDX_8A72A47315761DAB (competence_id), INDEX IDX_8A72A47389034830 (groupe_competence_id), PRIMARY KEY(competence_id, groupe_competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe_competence (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, descriptif LONGTEXT NOT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_2C3959A3642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe_tag (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, is_deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE niveau (id INT AUTO_INCREMENT NOT NULL, competence_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, critere_evaluation LONGTEXT NOT NULL, groupe_action LONGTEXT NOT NULL, INDEX IDX_4BDFF36B15761DAB (competence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, presentation LONGTEXT NOT NULL, critere_admission LONGTEXT NOT NULL, critere_evaluation LONGTEXT NOT NULL, programme LONGBLOB DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_groupe_competence (referentiel_id INT NOT NULL, groupe_competence_id INT NOT NULL, INDEX IDX_EC387D5B805DB139 (referentiel_id), INDEX IDX_EC387D5B89034830 (groupe_competence_id), PRIMARY KEY(referentiel_id, groupe_competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, descriptif LONGTEXT NOT NULL, is_deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_groupe_tag (tag_id INT NOT NULL, groupe_tag_id INT NOT NULL, INDEX IDX_2932D77FBAD26311 (tag_id), INDEX IDX_2932D77FD1EC9F2B (groupe_tag_id), PRIMARY KEY(tag_id, groupe_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, statut TINYINT(1) NOT NULL, avatar LONGBLOB DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, dtype VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649275ED078 (profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competence_groupe_competence ADD CONSTRAINT FK_8A72A47315761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence_groupe_competence ADD CONSTRAINT FK_8A72A47389034830 FOREIGN KEY (groupe_competence_id) REFERENCES groupe_competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_competence ADD CONSTRAINT FK_2C3959A3642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE niveau ADD CONSTRAINT FK_4BDFF36B15761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE referentiel_groupe_competence ADD CONSTRAINT FK_EC387D5B805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_groupe_competence ADD CONSTRAINT FK_EC387D5B89034830 FOREIGN KEY (groupe_competence_id) REFERENCES groupe_competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FD1EC9F2B FOREIGN KEY (groupe_tag_id) REFERENCES groupe_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE groupes CHANGE date_creation date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE groupes_formateur ADD CONSTRAINT FK_9481F39E155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFF305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFFC5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos DROP reference_agate, CHANGE date_debut date_debut DATE NOT NULL');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F705805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id)');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competence_groupe_competence DROP FOREIGN KEY FK_8A72A47315761DAB');
        $this->addSql('ALTER TABLE niveau DROP FOREIGN KEY FK_4BDFF36B15761DAB');
        $this->addSql('ALTER TABLE competence_groupe_competence DROP FOREIGN KEY FK_8A72A47389034830');
        $this->addSql('ALTER TABLE referentiel_groupe_competence DROP FOREIGN KEY FK_EC387D5B89034830');
        $this->addSql('ALTER TABLE tag_groupe_tag DROP FOREIGN KEY FK_2932D77FD1EC9F2B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649275ED078');
        $this->addSql('ALTER TABLE promos DROP FOREIGN KEY FK_31D1F705805DB139');
        $this->addSql('ALTER TABLE referentiel_groupe_competence DROP FOREIGN KEY FK_EC387D5B805DB139');
        $this->addSql('ALTER TABLE tag_groupe_tag DROP FOREIGN KEY FK_2932D77FBAD26311');
        $this->addSql('ALTER TABLE groupe_competence DROP FOREIGN KEY FK_2C3959A3642B8210');
        $this->addSql('ALTER TABLE groupes_formateur DROP FOREIGN KEY FK_9481F39E155D8F51');
        $this->addSql('ALTER TABLE groupes_apprenant DROP FOREIGN KEY FK_BD1CCBFFC5697D6D');
        $this->addSql('ALTER TABLE promos_formateur DROP FOREIGN KEY FK_70F76221155D8F51');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE competence_groupe_competence');
        $this->addSql('DROP TABLE groupe_competence');
        $this->addSql('DROP TABLE groupe_tag');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE referentiel');
        $this->addSql('DROP TABLE referentiel_groupe_competence');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_groupe_tag');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE groupes CHANGE date_creation date_creation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE groupes_apprenant DROP FOREIGN KEY FK_BD1CCBFF305371B');
        $this->addSql('ALTER TABLE promos ADD reference_agate VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_debut date_debut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE promos_formateur DROP FOREIGN KEY FK_70F76221CAA392D2');
    }
}
