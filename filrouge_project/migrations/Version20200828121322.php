<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200828121322 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800AAAE8765 FOREIGN KEY (brief_la_id) REFERENCES brief_la (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800AAAE8765 ON livrables (brief_la_id)');
        $this->addSql('ALTER TABLE ressource CHANGE piece_jointe piece_jointe LONGBLOB NOT NULL');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_groupe_tag ADD CONSTRAINT FK_2932D77FD1EC9F2B FOREIGN KEY (groupe_tag_id) REFERENCES groupe_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496409EF73 FOREIGN KEY (profil_sortie_id) REFERENCES profil_sortie (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800AAAE8765');
        $this->addSql('DROP INDEX IDX_FF9E7800AAAE8765 ON livrables');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544757FABFF');
        $this->addSql('ALTER TABLE ressource CHANGE piece_jointe piece_jointe LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE tag_groupe_tag DROP FOREIGN KEY FK_2932D77FBAD26311');
        $this->addSql('ALTER TABLE tag_groupe_tag DROP FOREIGN KEY FK_2932D77FD1EC9F2B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649275ED078');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496409EF73');
    }
}
