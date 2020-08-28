<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827151846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promos ADD fil_de_discussion_id INT NOT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE promos ADD CONSTRAINT FK_31D1F7059E665F32 FOREIGN KEY (fil_de_discussion_id) REFERENCES fil_de_discussion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31D1F7059E665F32 ON promos (fil_de_discussion_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promos DROP FOREIGN KEY FK_31D1F7059E665F32');
        $this->addSql('DROP INDEX UNIQ_31D1F7059E665F32 ON promos');
        $this->addSql('ALTER TABLE promos DROP fil_de_discussion_id, CHANGE lieu lieu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
