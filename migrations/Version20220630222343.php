<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630222343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar CHANGE category category_id INT NOT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A14612469DE2 FOREIGN KEY (category_id) REFERENCES programming_language (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A14612469DE2 ON calendar (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A14612469DE2');
        $this->addSql('DROP INDEX IDX_6EA9A14612469DE2 ON calendar');
        $this->addSql('ALTER TABLE calendar CHANGE category_id category INT NOT NULL');
    }
}
