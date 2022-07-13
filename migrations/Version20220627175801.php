<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627175801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, category INT NOT NULL, created_at DATETIME NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, title VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, background_color VARCHAR(7) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, teacher_id INT DEFAULT NULL, teacher_name VARCHAR(255) DEFAULT NULL, INDEX IDX_6EA9A146613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone INT NOT NULL, objet VARCHAR(255) NOT NULL, message TINYTEXT NOT NULL, created_at DATETIME NOT NULL, lu TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, link VARCHAR(255) NOT NULL, INDEX IDX_169E6FB912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, teacher_id INT NOT NULL, session_id INT NOT NULL, grade INT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, INDEX IDX_595AAE34A76ED395 (user_id), INDEX IDX_595AAE3412469DE2 (category_id), INDEX IDX_595AAE3441807E1D (teacher_id), INDEX IDX_595AAE34613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, file VARCHAR(255) DEFAULT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, link VARCHAR(255) DEFAULT NULL, is_read TINYINT(1) DEFAULT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, tarification INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programming_language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', start_session DATETIME NOT NULL, end_session DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, fullname VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, checking TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, sexe TINYINT(1) NOT NULL, is_teacher TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649613FECDF (session_id), INDEX IDX_8D93D6494C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB912469DE2 FOREIGN KEY (category_id) REFERENCES programming_language (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE3412469DE2 FOREIGN KEY (category_id) REFERENCES programming_language (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE3441807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE34613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494C3A3BB');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB912469DE2');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE3412469DE2');
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146613FECDF');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE34613FECDF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649613FECDF');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE34A76ED395');
        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE3441807E1D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE programming_language');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE user');
    }
}
