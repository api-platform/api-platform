<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215111628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE nv_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_carrier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_order_item_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_channel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_admin_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE nv_product_variant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE nv_category (id INT NOT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_product (id INT NOT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enable BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_order (id INT NOT NULL, channel_id INT NOT NULL, customer_id INT NOT NULL, state VARCHAR(255) NOT NULL, total NUMERIC(5, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_carrier (id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, enable BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_address (id INT NOT NULL, customer_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, post_code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_order_item (id INT NOT NULL, order_id INT NOT NULL, quantity INT NOT NULL, total NUMERIC(10, 0) NOT NULL, product_name VARCHAR(255) NOT NULL, variant_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_payment (id INT NOT NULL, method_id INT NOT NULL, amount NUMERIC(10, 0) NOT NULL, state VARCHAR(255) NOT NULL, details VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_customer (id INT NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_day TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, gender VARCHAR(255) NOT NULL, subscribed_to_newsletter BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_channel (id INT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, enable BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, contact_email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_admin_user (id INT NOT NULL, user_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enable VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE nv_product_variant (id INT NOT NULL, product_id INT NOT NULL, code VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE nv_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_carrier_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_order_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_customer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_channel_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_admin_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE nv_product_variant_id_seq CASCADE');
        $this->addSql('DROP TABLE nv_category');
        $this->addSql('DROP TABLE nv_product');
        $this->addSql('DROP TABLE nv_order');
        $this->addSql('DROP TABLE nv_carrier');
        $this->addSql('DROP TABLE nv_address');
        $this->addSql('DROP TABLE nv_order_item');
        $this->addSql('DROP TABLE nv_payment');
        $this->addSql('DROP TABLE nv_customer');
        $this->addSql('DROP TABLE nv_channel');
        $this->addSql('DROP TABLE nv_admin_user');
        $this->addSql('DROP TABLE nv_product_variant');
    }
}
