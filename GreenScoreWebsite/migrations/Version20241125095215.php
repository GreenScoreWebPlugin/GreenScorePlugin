<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125095215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation CHANGE siret siret VARCHAR(14) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD organisation_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496C6A4201 FOREIGN KEY (organisation_id_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496C6A4201 ON user (organisation_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6496C6A4201');
        $this->addSql('DROP INDEX IDX_8D93D6496C6A4201 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP organisation_id_id');
        $this->addSql('ALTER TABLE organisation CHANGE siret siret VARCHAR(14) NOT NULL');
    }
}
