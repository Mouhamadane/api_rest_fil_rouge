<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200826163542 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brief_la (id INT AUTO_INCREMENT NOT NULL, brief_id INT DEFAULT NULL, livrable_attendu_id INT DEFAULT NULL, INDEX IDX_7862AB35757FABFF (brief_id), INDEX IDX_7862AB3575180ACC (livrable_attendu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statistiques_competences (id INT AUTO_INCREMENT NOT NULL, referentiel_id INT DEFAULT NULL, competence_id INT DEFAULT NULL, promos_id INT DEFAULT NULL, apprenant_id INT DEFAULT NULL, niveau1 TINYINT(1) NOT NULL, niveau2 TINYINT(1) NOT NULL, niveau3 TINYINT(1) NOT NULL, INDEX IDX_5C1C9F22805DB139 (referentiel_id), INDEX IDX_5C1C9F2215761DAB (competence_id), INDEX IDX_5C1C9F22CAA392D2 (promos_id), INDEX IDX_5C1C9F22C5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE brief_la ADD CONSTRAINT FK_7862AB35757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        $this->addSql('ALTER TABLE brief_la ADD CONSTRAINT FK_7862AB3575180ACC FOREIGN KEY (livrable_attendu_id) REFERENCES livrables_attendus (id)');
        $this->addSql('ALTER TABLE statistiques_competences ADD CONSTRAINT FK_5C1C9F22805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id)');
        $this->addSql('ALTER TABLE statistiques_competences ADD CONSTRAINT FK_5C1C9F2215761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE statistiques_competences ADD CONSTRAINT FK_5C1C9F22CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id)');
        $this->addSql('ALTER TABLE statistiques_competences ADD CONSTRAINT FK_5C1C9F22C5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE livrables_attendus_brief');
        $this->addSql('DROP TABLE promo_brief_apprenant');
        $this->addSql('ALTER TABLE livrable_partiels ADD brief_la_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livrable_partiels ADD CONSTRAINT FK_F0370946AAAE8765 FOREIGN KEY (brief_la_id) REFERENCES brief_la (id)');
        $this->addSql('CREATE INDEX IDX_F0370946AAAE8765 ON livrable_partiels (brief_la_id)');
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800251E52B2');
        $this->addSql('DROP INDEX IDX_FF9E7800251E52B2 ON livrables');
        $this->addSql('ALTER TABLE livrables DROP livrables_attendus_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livrable_partiels DROP FOREIGN KEY FK_F0370946AAAE8765');
        $this->addSql('CREATE TABLE livrables_attendus_brief (livrables_attendus_id INT NOT NULL, brief_id INT NOT NULL, INDEX IDX_B2E6638E251E52B2 (livrables_attendus_id), INDEX IDX_B2E6638E757FABFF (brief_id), PRIMARY KEY(livrables_attendus_id, brief_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE promo_brief_apprenant (id INT AUTO_INCREMENT NOT NULL, apprenant_id INT DEFAULT NULL, promo_brief_id INT DEFAULT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_A9D0C93CBDA08EC7 (promo_brief_id), INDEX IDX_A9D0C93CC5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE livrables_attendus_brief ADD CONSTRAINT FK_B2E6638E251E52B2 FOREIGN KEY (livrables_attendus_id) REFERENCES livrables_attendus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livrables_attendus_brief ADD CONSTRAINT FK_B2E6638E757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93CBDA08EC7 FOREIGN KEY (promo_brief_id) REFERENCES promo_brief (id)');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93CC5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE brief_la');
        $this->addSql('DROP TABLE statistiques_competences');
        $this->addSql('DROP INDEX IDX_F0370946AAAE8765 ON livrable_partiels');
        $this->addSql('ALTER TABLE livrable_partiels DROP brief_la_id');
        $this->addSql('ALTER TABLE livrables ADD livrables_attendus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800251E52B2 FOREIGN KEY (livrables_attendus_id) REFERENCES livrables_attendus (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800251E52B2 ON livrables (livrables_attendus_id)');
    }
}
