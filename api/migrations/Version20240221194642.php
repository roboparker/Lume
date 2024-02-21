<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221194642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ownership to note card entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_card ADD owned_by_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN note_card.owned_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE note_card ADD CONSTRAINT FK_B66E898B5E70BCD7 FOREIGN KEY (owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B66E898B5E70BCD7 ON note_card (owned_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE note_card DROP CONSTRAINT FK_B66E898B5E70BCD7');
        $this->addSql('DROP INDEX IDX_B66E898B5E70BCD7');
        $this->addSql('ALTER TABLE note_card DROP owned_by_id');
    }
}
