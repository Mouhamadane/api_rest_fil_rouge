<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200825001100 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_brief DROP FOREIGN KEY FK_F6922C91D965FF70');
        $this->addSql('DROP INDEX IDX_F6922C91D965FF70 ON promo_brief');
        $this->addSql('ALTER TABLE promo_brief DROP promosbrief_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promo_brief ADD promosbrief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE promo_brief ADD CONSTRAINT FK_F6922C91D965FF70 FOREIGN KEY (promosbrief_id) REFERENCES promos (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F6922C91D965FF70 ON promo_brief (promosbrief_id)');
    }
}
