<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220101602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AF675F31B');
        $this->addSql('DROP INDEX IDX_2F57B37AF675F31B ON figure');
        $this->addSql('ALTER TABLE figure CHANGE author_id pseudo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A20E394C2 FOREIGN KEY (pseudo_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37A20E394C2 ON figure (pseudo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A20E394C2');
        $this->addSql('DROP INDEX IDX_2F57B37A20E394C2 ON figure');
        $this->addSql('ALTER TABLE figure CHANGE pseudo_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AF675F31B ON figure (author_id)');
    }
}
