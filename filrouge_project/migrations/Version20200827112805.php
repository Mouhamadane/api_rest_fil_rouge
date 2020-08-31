<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827112805 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_brief_apprenant DROP FOREIGN KEY FK_A9D0C93C943F2B0');
        $this->addSql('DROP INDEX IDX_A9D0C93C943F2B0 ON promo_brief_apprenant');
        $this->addSql('ALTER TABLE promo_brief_apprenant DROP statut, CHANGE promobrief_id brief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93C757FABFF FOREIGN KEY (brief_id) REFERENCES brief (id)');
        $this->addSql('CREATE INDEX IDX_A9D0C93C757FABFF ON promo_brief_apprenant (brief_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_brief_apprenant DROP FOREIGN KEY FK_A9D0C93C757FABFF');
        $this->addSql('DROP INDEX IDX_A9D0C93C757FABFF ON promo_brief_apprenant');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE brief_id promobrief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promo_brief_apprenant ADD CONSTRAINT FK_A9D0C93C943F2B0 FOREIGN KEY (promobrief_id) REFERENCES brief (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A9D0C93C943F2B0 ON promo_brief_apprenant (promobrief_id)');
    }
}
