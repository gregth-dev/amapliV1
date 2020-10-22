<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827201606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract_member (id INT AUTO_INCREMENT NOT NULL, contract_id INT NOT NULL, subscriber_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4CA41F352576E0FD (contract_id), INDEX IDX_4CA41F357808B1AD (subscriber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, contract_member_id INT NOT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, INDEX IDX_F52993984584665A (product_id), INDEX IDX_F529939858008B68 (contract_member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_delivery (order_id INT NOT NULL, delivery_id INT NOT NULL, INDEX IDX_D6790EA18D9F6D38 (order_id), INDEX IDX_D6790EA112136921 (delivery_id), PRIMARY KEY(order_id, delivery_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, contract_member_id INT NOT NULL, check_number VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, deposit_date DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6D28840D58008B68 (contract_member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_order (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT NOT NULL, command_id INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_5475E8C47808B1AD (subscriber_id), INDEX IDX_5475E8C433E1689A (command_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contract_member ADD CONSTRAINT FK_4CA41F352576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE contract_member ADD CONSTRAINT FK_4CA41F357808B1AD FOREIGN KEY (subscriber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939858008B68 FOREIGN KEY (contract_member_id) REFERENCES contract_member (id)');
        $this->addSql('ALTER TABLE order_delivery ADD CONSTRAINT FK_D6790EA18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_delivery ADD CONSTRAINT FK_D6790EA112136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D58008B68 FOREIGN KEY (contract_member_id) REFERENCES contract_member (id)');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C47808B1AD FOREIGN KEY (subscriber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_order ADD CONSTRAINT FK_5475E8C433E1689A FOREIGN KEY (command_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939858008B68');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D58008B68');
        $this->addSql('ALTER TABLE order_delivery DROP FOREIGN KEY FK_D6790EA18D9F6D38');
        $this->addSql('ALTER TABLE product_order DROP FOREIGN KEY FK_5475E8C433E1689A');
        $this->addSql('DROP TABLE contract_member');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_delivery');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product_order');
    }
}
