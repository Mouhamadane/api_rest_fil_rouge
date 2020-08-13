<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200812183804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496409EF73');
        $this->addSql('CREATE TABLE competence_groupe_competence (competence_id INT NOT NULL, groupe_competence_id INT NOT NULL, INDEX IDX_8A72A47315761DAB (competence_id), INDEX IDX_8A72A47389034830 (groupe_competence_id), PRIMARY KEY(competence_id, groupe_competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promos_formateur (promos_id INT NOT NULL, formateur_id INT NOT NULL, INDEX IDX_70F76221CAA392D2 (promos_id), INDEX IDX_70F76221155D8F51 (formateur_id), PRIMARY KEY(promos_id, formateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_groupe_competence (referentiel_id INT NOT NULL, groupe_competence_id INT NOT NULL, INDEX IDX_EC387D5B805DB139 (referentiel_id), INDEX IDX_EC387D5B89034830 (groupe_competence_id), PRIMARY KEY(referentiel_id, groupe_competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_groupe_tag (tag_id INT NOT NULL, groupe_tag_id INT NOT NULL, INDEX IDX_2932D77FBAD26311 (tag_id), INDEX IDX_2932D77FD1EC9F2B (groupe_tag_id), PRIMARY KEY(tag_id, groupe_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competence_groupe_competence ADD CONSTRAINT FK_8A72A47315761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competence_groupe_competence ADD CONSTRAINT FK_8A72A47389034830 FOREIGN KEY (groupe_competence_id) REFERENCES groupe_competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_groupe_competence ADD CONSTRAINT FK_EC387D5B805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_groupe_competence ADD CONSTRAINT FK_EC387D5B89034830 FOREIGN KEY (groupe_competence_id) REFERENCES groupe_competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FD1EC9F2B FOREIGN KEY (groupe_tag_id) REFERENCES groupe_tag (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE formateur_promos');
        $this->addSql('DROP TABLE groupe_competence_competence');
        $this->addSql('DROP TABLE groupe_tag_tag');
        $this->addSql('DROP TABLE profil_sortie');
        $this->addSql('DROP TABLE referentiel_competence');
        $this->addSql('ALTER TABLE competence ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE groupe_competence DROP FOREIGN KEY FK_2C3959A3A76ED395');
        $this->addSql('DROP INDEX IDX_2C3959A3A76ED395 ON groupe_competence');
        $this->addSql('ALTER TABLE groupe_competence ADD is_deleted TINYINT(1) NOT NULL, CHANGE descriptif descriptif LONGTEXT NOT NULL, CHANGE user_id admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe_competence ADD CONSTRAINT FK_2C3959A3642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2C3959A3642B8210 ON groupe_competence (admin_id)');
        $this->addSql('ALTER TABLE groupe_tag ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE niveau CHANGE critere_evaluation critere_evaluation LONGTEXT NOT NULL, CHANGE groupe_actions groupe_action LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_E6D6B297A4D60759 ON profil');
        $this->addSql('ALTER TABLE promos ADD date_provisoire DATE NOT NULL, ADD date_fin DATE DEFAULT NULL, DROP reference_agate, DROP date_fin_provisoire, DROP date_fin_reelle, DROP etat');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD is_deleted TINYINT(1) NOT NULL, CHANGE descriptif descriptif LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX IDX_8D93D6496409EF73 ON user');
        $this->addSql('ALTER TABLE user ADD is_deleted TINYINT(1) NOT NULL, DROP profil_sortie_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formateur_promos (formateur_id INT NOT NULL, promos_id INT NOT NULL, INDEX IDX_190EF52D155D8F51 (formateur_id), INDEX IDX_190EF52DCAA392D2 (promos_id), PRIMARY KEY(formateur_id, promos_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE groupe_competence_competence (groupe_competence_id INT NOT NULL, competence_id INT NOT NULL, INDEX IDX_F64AE85C89034830 (groupe_competence_id), INDEX IDX_F64AE85C15761DAB (competence_id), PRIMARY KEY(groupe_competence_id, competence_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE groupe_tag_tag (groupe_tag_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_C430CACFD1EC9F2B (groupe_tag_id), INDEX IDX_C430CACFBAD26311 (tag_id), PRIMARY KEY(groupe_tag_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE profil_sortie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE referentiel_competence (referentiel_id INT NOT NULL, competence_id INT NOT NULL, INDEX IDX_2377878B805DB139 (referentiel_id), INDEX IDX_2377878B15761DAB (competence_id), PRIMARY KEY(referentiel_id, competence_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE formateur_promos ADD CONSTRAINT FK_190EF52D155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formateur_promos ADD CONSTRAINT FK_190EF52DCAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_competence_competence ADD CONSTRAINT FK_F64AE85C15761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_competence_competence ADD CONSTRAINT FK_F64AE85C89034830 FOREIGN KEY (groupe_competence_id) REFERENCES groupe_competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_tag_tag ADD CONSTRAINT FK_C430CACFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupe_tag_tag ADD CONSTRAINT FK_C430CACFD1EC9F2B FOREIGN KEY (groupe_tag_id) REFERENCES groupe_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_competence ADD CONSTRAINT FK_2377878B15761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel_competence ADD CONSTRAINT FK_2377878B805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE competence_groupe_competence');
        $this->addSql('DROP TABLE promos_formateur');
        $this->addSql('DROP TABLE referentiel_groupe_competence');
        $this->addSql('DROP TABLE tag_groupe_tag');
        $this->addSql('ALTER TABLE competence DROP is_deleted');
        $this->addSql('ALTER TABLE groupe_competence DROP FOREIGN KEY FK_2C3959A3642B8210');
        $this->addSql('DROP INDEX IDX_2C3959A3642B8210 ON groupe_competence');
        $this->addSql('ALTER TABLE groupe_competence DROP is_deleted, CHANGE descriptif descriptif VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE admin_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE groupe_competence ADD CONSTRAINT FK_2C3959A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2C3959A3A76ED395 ON groupe_competence (user_id)');
        $this->addSql('ALTER TABLE groupe_tag DROP is_deleted');
        $this->addSql('ALTER TABLE niveau CHANGE critere_evaluation critere_evaluation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE groupe_action groupe_actions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6D6B297A4D60759 ON profil (libelle)');
        $this->addSql('ALTER TABLE promos ADD reference_agate VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD date_fin_reelle DATE NOT NULL, ADD etat TINYINT(1) NOT NULL, DROP date_fin, CHANGE date_provisoire date_fin_provisoire DATE NOT NULL');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tag DROP is_deleted, CHANGE descriptif descriptif VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user ADD profil_sortie_id INT DEFAULT NULL, DROP is_deleted');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496409EF73 FOREIGN KEY (profil_sortie_id) REFERENCES profil_sortie (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496409EF73 ON user (profil_sortie_id)');
    }
}
