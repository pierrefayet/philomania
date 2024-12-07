<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207104041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme ADD synthesize_id INT DEFAULT NULL, DROP synthesize');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708E7F88F4B FOREIGN KEY (synthesize_id) REFERENCES synthesis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E708E7F88F4B ON theme (synthesize_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708E7F88F4B');
        $this->addSql('DROP INDEX UNIQ_9775E708E7F88F4B ON theme');
        $this->addSql('ALTER TABLE theme ADD synthesize LONGTEXT DEFAULT NULL, DROP synthesize_id');
    }
}
