<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222165716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synthesis CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708E7F88F4B');
        $this->addSql('DROP INDEX UNIQ_9775E708E7F88F4B ON theme');
        $this->addSql('ALTER TABLE theme CHANGE synthesize_id synthesis_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708EC91FE48 FOREIGN KEY (synthesis_id) REFERENCES synthesis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E708EC91FE48 ON theme (synthesis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708EC91FE48');
        $this->addSql('DROP INDEX UNIQ_9775E708EC91FE48 ON theme');
        $this->addSql('ALTER TABLE theme CHANGE synthesis_id synthesize_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708E7F88F4B FOREIGN KEY (synthesize_id) REFERENCES synthesis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E708E7F88F4B ON theme (synthesize_id)');
        $this->addSql('ALTER TABLE synthesis CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
