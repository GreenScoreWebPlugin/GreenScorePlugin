<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105005425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advice (id INT AUTO_INCREMENT NOT NULL, advice LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equivalent (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, carbon_kg_co2 DOUBLE PRECISION NOT NULL, equivalent DOUBLE PRECISION NOT NULL, icon_thumbnail VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitored_website (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, url_domain VARCHAR(255) DEFAULT NULL, url_full LONGTEXT DEFAULT NULL, queries_quantity INT DEFAULT NULL, carbon_footprint DOUBLE PRECISION DEFAULT NULL, data_transferred DOUBLE PRECISION DEFAULT NULL, resources LONGTEXT DEFAULT NULL, loading_time DOUBLE PRECISION DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, creation_date DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_7458B0D5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, organisation_name VARCHAR(255) NOT NULL, organisation_code VARCHAR(20) NOT NULL, city VARCHAR(255) DEFAULT NULL, siret VARCHAR(14) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, organisation_id INT DEFAULT NULL, is_admin_of_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, total_carbon_footprint DOUBLE PRECISION DEFAULT NULL, INDEX IDX_8D93D6499E6B1585 (organisation_id), UNIQUE INDEX UNIQ_8D93D6492B4135F3 (is_admin_of_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monitored_website ADD CONSTRAINT FK_7458B0D5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6499E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492B4135F3 FOREIGN KEY (is_admin_of_id) REFERENCES organisation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE monitored_website DROP FOREIGN KEY FK_7458B0D5A76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6499E6B1585');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492B4135F3');
        $this->addSql('DROP TABLE advice');
        $this->addSql('DROP TABLE equivalent');
        $this->addSql('DROP TABLE monitored_website');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
