<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703123130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forms (id INT AUTO_INCREMENT NOT NULL, template_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, answers JSON NOT NULL, INDEX IDX_FD3F1BF75DA0FB8 (template_id), INDEX IDX_FD3F1BF7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, topic VARCHAR(255) NOT NULL, tags JSON NOT NULL, is_public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6F287D8EB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_allowed_users (template_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CC69CE165DA0FB8 (template_id), INDEX IDX_CC69CE16A76ED395 (user_id), PRIMARY KEY(template_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, roles JSON NOT NULL, is_blocked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forms ADD CONSTRAINT FK_FD3F1BF75DA0FB8 FOREIGN KEY (template_id) REFERENCES templates (id)');
        $this->addSql('ALTER TABLE forms ADD CONSTRAINT FK_FD3F1BF7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8EB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE template_allowed_users ADD CONSTRAINT FK_CC69CE165DA0FB8 FOREIGN KEY (template_id) REFERENCES templates (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template_allowed_users ADD CONSTRAINT FK_CC69CE16A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forms DROP FOREIGN KEY FK_FD3F1BF75DA0FB8');
        $this->addSql('ALTER TABLE forms DROP FOREIGN KEY FK_FD3F1BF7A76ED395');
        $this->addSql('ALTER TABLE templates DROP FOREIGN KEY FK_6F287D8EB03A8386');
        $this->addSql('ALTER TABLE template_allowed_users DROP FOREIGN KEY FK_CC69CE165DA0FB8');
        $this->addSql('ALTER TABLE template_allowed_users DROP FOREIGN KEY FK_CC69CE16A76ED395');
        $this->addSql('DROP TABLE forms');
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP TABLE template_allowed_users');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
