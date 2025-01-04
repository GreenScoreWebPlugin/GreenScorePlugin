<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250104231223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation DROP email, DROP password, CHANGE city city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD is_admin_of_id INT DEFAULT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492B4135F3 FOREIGN KEY (is_admin_of_id) REFERENCES organisation (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6492B4135F3 ON user (is_admin_of_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492B4135F3');
        $this->addSql('DROP INDEX UNIQ_8D93D6492B4135F3 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP is_admin_of_id, CHANGE first_name first_name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE organisation ADD email VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL');
    }
}
