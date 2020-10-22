<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200828150633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, contract_member_id INT NOT NULL, producer_id INT NOT NULL, check_number VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, deposit_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6D28840D58008B68 (contract_member_id), INDEX IDX_6D28840D89B658FE (producer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permanence (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, number_places INT NOT NULL, informations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permanence_user (permanence_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CCB60EA1A9457964 (permanence_id), INDEX IDX_CCB60EA1A76ED395 (user_id), PRIMARY KEY(permanence_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D58008B68 FOREIGN KEY (contract_member_id) REFERENCES contract_member (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D89B658FE FOREIGN KEY (producer_id) REFERENCES producer (id)');
        $this->addSql('ALTER TABLE permanence_user ADD CONSTRAINT FK_CCB60EA1A9457964 FOREIGN KEY (permanence_id) REFERENCES permanence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permanence_user ADD CONSTRAINT FK_CCB60EA1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE permanence_user DROP FOREIGN KEY FK_CCB60EA1A9457964');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE permanence');
        $this->addSql('DROP TABLE permanence_user');
    }
}
