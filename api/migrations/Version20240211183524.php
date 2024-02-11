<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211183524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Deck entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deck (id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN deck.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE deck_note_card (deck_id UUID NOT NULL, note_card_id UUID NOT NULL, PRIMARY KEY(deck_id, note_card_id))');
        $this->addSql('CREATE INDEX IDX_6357FFDC111948DC ON deck_note_card (deck_id)');
        $this->addSql('CREATE INDEX IDX_6357FFDCCB6A5EBA ON deck_note_card (note_card_id)');
        $this->addSql('COMMENT ON COLUMN deck_note_card.deck_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN deck_note_card.note_card_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE deck_note_card ADD CONSTRAINT FK_6357FFDC111948DC FOREIGN KEY (deck_id) REFERENCES deck (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE deck_note_card ADD CONSTRAINT FK_6357FFDCCB6A5EBA FOREIGN KEY (note_card_id) REFERENCES note_card (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE deck_note_card DROP CONSTRAINT FK_6357FFDC111948DC');
        $this->addSql('ALTER TABLE deck_note_card DROP CONSTRAINT FK_6357FFDCCB6A5EBA');
        $this->addSql('DROP TABLE deck');
        $this->addSql('DROP TABLE deck_note_card');
    }
}
