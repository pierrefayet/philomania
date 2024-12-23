<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222173421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synthesis CHANGE title title LONGTEXT NOT NULL, CHANGE content content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme CHANGE title title LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme CHANGE title title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE synthesis CHANGE title title VARCHAR(255) NOT NULL, CHANGE content content VARCHAR(255) DEFAULT NULL');
    }
}
