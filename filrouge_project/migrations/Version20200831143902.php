<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831143902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brief CHANGE livrables_attendus livrables_attendus VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800AAAE8765 FOREIGN KEY (brief_la_id) REFERENCES brief_la (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800AAAE8765 ON livrables (brief_la_id)');
        $this->addSql('ALTER TABLE niveau DROP critere_evaluation');
        $this->addSql('ALTER TABLE promos ADD fil_de_discussion_id INT NOT NULL');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F7059E665F32 FOREIGN KEY (fil_de_discussion_id) REFERENCES fil_de_discussion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31D1F7059E665F32 ON promos (fil_de_discussion_id)');
        $this->addSql('ALTER TABLE ressource CHANGE piece_jointe piece_jointe LONGBLOB NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brief CHANGE livrables_attendus livrables_attendus LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800AAAE8765');
        $this->addSql('DROP INDEX IDX_FF9E7800AAAE8765 ON livrables');
        $this->addSql('ALTER TABLE niveau ADD critere_evaluation LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE promos DROP FOREIGN KEY FK_31D1F7059E665F32');
        $this->addSql('DROP INDEX UNIQ_31D1F7059E665F32 ON promos');
        $this->addSql('ALTER TABLE promos DROP fil_de_discussion_id');
        $this->addSql('ALTER TABLE ressource CHANGE piece_jointe piece_jointe LONGBLOB DEFAULT NULL');
    }
}
