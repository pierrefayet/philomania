<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209014435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synthesis ADD theme_id INT NOT NULL');
        $this->addSql('ALTER TABLE synthesis ADD CONSTRAINT FK_593C04B659027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE INDEX IDX_593C04B659027487 ON synthesis (theme_id)');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708EC91FE48');
        $this->addSql('DROP INDEX UNIQ_9775E708EC91FE48 ON theme');
        $this->addSql('ALTER TABLE theme DROP synthesis_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme ADD synthesis_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708EC91FE48 FOREIGN KEY (synthesis_id) REFERENCES synthesis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E708EC91FE48 ON theme (synthesis_id)');
        $this->addSql('ALTER TABLE synthesis DROP FOREIGN KEY FK_593C04B659027487');
        $this->addSql('DROP INDEX IDX_593C04B659027487 ON synthesis');
        $this->addSql('ALTER TABLE synthesis DROP theme_id');
    }
}
