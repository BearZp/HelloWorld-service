<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323082324 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_directory_question_level table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        $this->addSql('create table kyc_directory_question_level (
	question_id char(32) not null,
	level_id int not null,
	directory_name varchar (255) not null
);');
        $this->addSql('create unique index kyc_directory_question_level_question_id_uindex
	on kyc_directory_question_level (question_id);');

        $this->addSql('alter table kyc_directory_question_level
	add constraint kyc_directory_question_level_kyc_question_id_fk
		foreign key (question_id) references kyc_question
			on update cascade on delete cascade;');

        $this->addSql('alter table kyc_directory_question_level
	add constraint kyc_directory_question_level_kyc_level_id_fk
		foreign key (level_id) references kyc_level
            on update cascade on delete restrict;;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE kyc_directory_question_level');
    }
}
