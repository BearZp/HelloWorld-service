<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210316145620 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_user_level table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('create table kyc_user_level (
	user_id varchar(255) not null,
	level_id int default 1 not null,
	next_level_id int,
	status int default 1 not null
);');

        $this->addSql('create unique index kyc_user_level_userid_uindex
	on kyc_user_level (user_id);');

        $this->addSql('alter table kyc_user_level
	add constraint kyc_user_level_pk
		primary key (user_id);');

        $this->addSql('alter table kyc_user_level
	add constraint kyc_user_level_kyc_level_id_fk
		foreign key (level_id) references kyc_level
			on update cascade on delete restrict;');

        $this->addSql('alter table kyc_user_level
	add constraint kyc_user_level_kyc_next_level_id_fk
		foreign key (next_level_id) references kyc_level
			on update cascade on delete restrict;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE kyc_user_level');
    }
}
