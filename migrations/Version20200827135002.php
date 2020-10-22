<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827135002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE producer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, referent_id INT NOT NULL, substitute_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, check_order VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_976449DCA76ED395 (user_id), INDEX IDX_976449DC35E47E35 (referent_id), INDEX IDX_976449DC19ECAD51 (substitute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, producer_id INT NOT NULL, name VARCHAR(255) NOT NULL, details VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_D34A04AD89B658FE (producer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE producer ADD CONSTRAINT FK_976449DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE producer ADD CONSTRAINT FK_976449DC35E47E35 FOREIGN KEY (referent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE producer ADD CONSTRAINT FK_976449DC19ECAD51 FOREIGN KEY (substitute_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD89B658FE FOREIGN KEY (producer_id) REFERENCES producer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD89B658FE');
        $this->addSql('DROP TABLE producer');
        $this->addSql('DROP TABLE product');
    }
}
