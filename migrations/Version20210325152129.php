<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325152129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_answer table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        $this->addSql('create table kyc_answer (
	id char(32) not null,
	question_id char(32) not null,
	user_id varchar(255) not null,
	level_id int not null,
	type int default 1 not null,
	status int default 2 not null,
	value text NOT NULL,
	PRIMARY KEY(id)
);');
        $this->addSql('create unique index kyc_answer_user_id_question_id_uindex
	on kyc_answer (user_id, question_id);');

        $this->addSql('alter table kyc_answer
	add constraint kyc_answer_kyc_question_id_fk
		foreign key (question_id) references kyc_question
			on update cascade on delete restrict;');

        $this->addSql('alter table kyc_answer
	add constraint kyc_answer_kyc_user_level_user_id_fk
		foreign key (user_id) references kyc_user_level (user_id)
            on update cascade on delete cascade;');

        $this->addSql('alter table kyc_answer
	add constraint kyc_answer_kyc_level_id_fk
		foreign key (level_id) references kyc_level
			on update cascade on delete cascade;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE kyc_answer');
    }
}
