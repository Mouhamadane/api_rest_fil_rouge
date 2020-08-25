<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200823232815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupes CHANGE date_creation date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE groupes_formateur ADD CONSTRAINT FK_9481F39E155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFF305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_apprenant ADD CONSTRAINT FK_BD1CCBFFC5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos ADD promosbrief_id INT DEFAULT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE date_debut date_debut DATE NOT NULL');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F705805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id)');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F705D965FF70 FOREIGN KEY (promosbrief_id) REFERENCES promo_brief (id)');
        $this->addSql('CREATE INDEX IDX_31D1F705D965FF70 ON promos (promosbrief_id)');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221CAA392D2 FOREIGN KEY (promos_id) REFERENCES promos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promos_formateur ADD CONSTRAINT FK_70F76221155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGBLOB DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupes CHANGE date_creation date_creation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE groupes_apprenant DROP FOREIGN KEY FK_BD1CCBFF305371B');
        $this->addSql('ALTER TABLE groupes_apprenant DROP FOREIGN KEY FK_BD1CCBFFC5697D6D');
        $this->addSql('ALTER TABLE groupes_formateur DROP FOREIGN KEY FK_9481F39E155D8F51');
        $this->addSql('ALTER TABLE promos DROP FOREIGN KEY FK_31D1F705805DB139');
        $this->addSql('ALTER TABLE promos DROP FOREIGN KEY FK_31D1F705D965FF70');
        $this->addSql('DROP INDEX IDX_31D1F705D965FF70 ON promos');
        $this->addSql('ALTER TABLE promos DROP promosbrief_id, CHANGE lieu lieu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE date_debut date_debut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE promos_formateur DROP FOREIGN KEY FK_70F76221CAA392D2');
        $this->addSql('ALTER TABLE promos_formateur DROP FOREIGN KEY FK_70F76221155D8F51');
        $this->addSql('ALTER TABLE referentiel CHANGE programme programme LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
