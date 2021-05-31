<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419080431 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('alter table kyc_user_level rename column status to next_level_status;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('alter table kyc_user_level rename column next_level_status to status;');
    }
}
