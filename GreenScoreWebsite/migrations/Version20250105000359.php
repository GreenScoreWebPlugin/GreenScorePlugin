<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105000359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE monitored_website (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, url_domain VARCHAR(255) DEFAULT NULL, url_full LONGTEXT DEFAULT NULL, queries_quantity INT DEFAULT NULL, carbon_footprint DOUBLE PRECISION DEFAULT NULL, data_transferred DOUBLE PRECISION DEFAULT NULL, resources LONGTEXT DEFAULT NULL, loading_time DOUBLE PRECISION DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, creation_date DATETIME NOT NULL DEFAULT current_timestamp(), INDEX IDX_7458B0D5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monitored_website ADD CONSTRAINT FK_7458B0D5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monitored_website DROP FOREIGN KEY FK_7458B0D5A76ED395');
        $this->addSql('DROP TABLE monitored_website');
    }
}
