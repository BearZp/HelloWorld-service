<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210318114957 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_question table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        $this->addSql('create table kyc_question (
	id char(32) not null,
	level_id int default 1 not null,
	question_title varchar (255) not null,
	question_description varchar (255) not null,
	sort_order int default 0 not null,
	type int default 1 not null,
	status int default 1 not null,
	PRIMARY KEY(id)
);');
        $this->addSql('alter table kyc_question
	add constraint kyc_question_kyc_level_id_fk
		foreign key (level_id) references kyc_level
			on update cascade on delete restrict;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE kyc_question');
    }
}
