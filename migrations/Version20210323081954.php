<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323081954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_directory table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('create sequence kyc_directory_id_seq;');

        $this->addSql('create table kyc_directory (
	id int NOT NULL default nextval(\'public.kyc_directory_id_seq\'), 
	directory_name varchar (255) not null,
	directory_value varchar (255) not null,
	sort_order int default 0 not null,
	status int default 1 not null,
	PRIMARY KEY(id)
);');

        $this->addSql('alter sequence kyc_directory_id_seq owned by kyc_directory.id;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE kyc_directory');
    }
}
