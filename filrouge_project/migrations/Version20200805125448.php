<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805125448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competence ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE groupe_competence ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE groupe_tag ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE tag ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_deleted TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competence DROP is_deleted');
        $this->addSql('ALTER TABLE groupe_competence DROP is_deleted');
        $this->addSql('ALTER TABLE groupe_tag DROP is_deleted');
        $this->addSql('ALTER TABLE tag DROP is_deleted');
        $this->addSql('ALTER TABLE user DROP is_deleted');
    }
}
