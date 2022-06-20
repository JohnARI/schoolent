<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513100027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE3441807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_595AAE3441807E1D ON grade (teacher_id)');
        $this->addSql('CREATE INDEX IDX_595AAE34613FECDF ON grade (session_id)');
        $this->addSql('ALTER TABLE notification ADD is_read TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE3441807E1D');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE34613FECDF');
        $this->addSql('DROP INDEX IDX_595AAE3441807E1D ON grade');
        $this->addSql('DROP INDEX IDX_595AAE34613FECDF ON grade');
        $this->addSql('ALTER TABLE notification DROP is_read');
    }
}
