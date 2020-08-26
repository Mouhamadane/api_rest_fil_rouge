<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200826192304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('DROP TABLE livrables_attendus_brief');
        // $this->addSql('ALTER TABLE brief_la ADD CONSTRAINT FK_7862AB35757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        // $this->addSql('ALTER TABLE brief_la ADD CONSTRAINT FK_7862AB3575180ACC FOREIGN KEY (livrable_attendu_id) REFERENCES livrables_attendus (id)');
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800251E52B2');
        $this->addSql('DROP INDEX IDX_FF9E7800251E52B2 ON livrables');
        $this->addSql('ALTER TABLE livrables CHANGE livrables_attendus_id brief_la_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800AAAE8765 FOREIGN KEY (brief_la_id) REFERENCES brief_la (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800AAAE8765 ON livrables (brief_la_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE livrables_attendus_brief (livrables_attendus_id INT NOT NULL, brief_id INT NOT NULL, INDEX IDX_B2E6638E251E52B2 (livrables_attendus_id), INDEX IDX_B2E6638E757FABFF (brief_id), PRIMARY KEY(livrables_attendus_id, brief_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        // $this->addSql('ALTER TABLE livrables_attendus_brief ADD CONSTRAINT FK_B2E6638E251E52B2 FOREIGN KEY (livrables_attendus_id) REFERENCES livrables_attendus (id) ON DELETE CASCADE');
        // $this->addSql('ALTER TABLE livrables_attendus_brief ADD CONSTRAINT FK_B2E6638E757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id) ON DELETE CASCADE');
        // $this->addSql('ALTER TABLE brief_la DROP FOREIGN KEY FK_7862AB35757FABFF');
        // $this->addSql('ALTER TABLE brief_la DROP FOREIGN KEY FK_7862AB3575180ACC');
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800AAAE8765');
        $this->addSql('DROP INDEX IDX_FF9E7800AAAE8765 ON livrables');
        $this->addSql('ALTER TABLE livrables CHANGE brief_la_id livrables_attendus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800251E52B2 FOREIGN KEY (livrables_attendus_id) REFERENCES livrables_attendus (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800251E52B2 ON livrables (livrables_attendus_id)');
    }
}
