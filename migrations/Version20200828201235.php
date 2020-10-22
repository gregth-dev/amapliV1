<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200828201235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE donation (id INT AUTO_INCREMENT NOT NULL, donor_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_31E581A03DD7B7A7 (donor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE donation_payment (id INT AUTO_INCREMENT NOT NULL, donation_id INT NOT NULL, check_number VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, deposit_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, check_order VARCHAR(255) NOT NULL, INDEX IDX_478478BD4DC1279C (donation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organism (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT NOT NULL, INDEX IDX_A3C664D37808B1AD (subscriber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_organism (subscription_id INT NOT NULL, organism_id INT NOT NULL, INDEX IDX_6AFDD5009A1887DC (subscription_id), INDEX IDX_6AFDD50064180A36 (organism_id), PRIMARY KEY(subscription_id, organism_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_payment (id INT AUTO_INCREMENT NOT NULL, subscription_id INT NOT NULL, check_number VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, deposit_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, check_order VARCHAR(255) NOT NULL, INDEX IDX_1E3D64969A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A03DD7B7A7 FOREIGN KEY (donor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE donation_payment ADD CONSTRAINT FK_478478BD4DC1279C FOREIGN KEY (donation_id) REFERENCES donation (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D37808B1AD FOREIGN KEY (subscriber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription_organism ADD CONSTRAINT FK_6AFDD5009A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_organism ADD CONSTRAINT FK_6AFDD50064180A36 FOREIGN KEY (organism_id) REFERENCES organism (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D64969A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation_payment DROP FOREIGN KEY FK_478478BD4DC1279C');
        $this->addSql('ALTER TABLE subscription_organism DROP FOREIGN KEY FK_6AFDD50064180A36');
        $this->addSql('ALTER TABLE subscription_organism DROP FOREIGN KEY FK_6AFDD5009A1887DC');
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D64969A1887DC');
        $this->addSql('DROP TABLE donation');
        $this->addSql('DROP TABLE donation_payment');
        $this->addSql('DROP TABLE organism');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_organism');
        $this->addSql('DROP TABLE subscription_payment');
    }
}
