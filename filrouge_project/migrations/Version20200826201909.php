<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200826201909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE promo_brief_apprenant (id INT AUTO_INCREMENT NOT NULL, promo_brief_id INT DEFAULT NULL, apprenant_id INT DEFAULT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_A9D0C93CBDA08EC7 (promo_brief_id), INDEX IDX_A9D0C93CC5697D6D (apprenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93CBDA08EC7 FOREIGN KEY (promo_brief_id) REFERENCES promo_brief (id)');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93CC5697D6D FOREIGN KEY (apprenant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE livrables ADD CONSTRAINT FK_FF9E7800AAAE8765 FOREIGN KEY (brief_la_id) REFERENCES brief_la (id)');
        $this->addSql('CREATE INDEX IDX_FF9E7800AAAE8765 ON livrables (brief_la_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE promo_brief_apprenant');
        $this->addSql('ALTER TABLE livrables DROP FOREIGN KEY FK_FF9E7800AAAE8765');
        $this->addSql('DROP INDEX IDX_FF9E7800AAAE8765 ON livrables');
    }
}
