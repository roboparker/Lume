<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212005720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_published to deck and note_card';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deck ADD is_published BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE note_card ADD is_published BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE note_card DROP is_published');
        $this->addSql('ALTER TABLE deck DROP is_published');
    }
}
