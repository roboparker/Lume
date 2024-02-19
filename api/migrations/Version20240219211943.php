<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219211943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add owned_by_id to deck';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deck ADD owned_by_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN deck.owned_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE deck ADD CONSTRAINT FK_4FAC36375E70BCD7 FOREIGN KEY (owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FAC36375E70BCD7 ON deck (owned_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE deck DROP CONSTRAINT FK_4FAC36375E70BCD7');
        $this->addSql('DROP INDEX IDX_4FAC36375E70BCD7');
        $this->addSql('ALTER TABLE deck DROP owned_by_id');
    }
}
