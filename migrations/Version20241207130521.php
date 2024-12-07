<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207130521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synthesis CHANGE theme_id_id theme_id INT NOT NULL');
        $this->addSql('ALTER TABLE synthesis ADD CONSTRAINT FK_593C04B659027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_593C04B659027487 ON synthesis (theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synthesis DROP FOREIGN KEY FK_593C04B659027487');
        $this->addSql('DROP INDEX UNIQ_593C04B659027487 ON synthesis');
        $this->addSql('ALTER TABLE synthesis CHANGE theme_id theme_id_id INT NOT NULL');
    }
}
