<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428093918 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('alter table kyc_answer drop column level_id;');
        $this->addSql('alter table kyc_question drop column level_id;');
        $this->addSql('alter table kyc_directory_question_level drop column level_id;');
        $this->addSql('alter table kyc_directory_question_level rename to kyc_directory_question;');
        $this->addSql('create table kyc_question_level (
            question_id char(32) not null,
            level_id int not null
        );');
    }

    public function down(Schema $schema) : void
    {
        // can't be downgraded
    }
}
