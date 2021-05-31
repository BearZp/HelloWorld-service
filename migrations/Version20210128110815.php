<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use common\lib\types\BoolType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Tools\models\mappers\MapperInterface;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use Tools\types\TextType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210128110815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create kyc_level table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE kyc_level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE kyc_level (
    id int NOT NULL default nextval(\'public.kyc_level_id_seq\'), 
    name varchar(255) NOT NULL, 
    description varchar(255) NOT NULL,
    advertising_text text NOT NULL,
    parent_level_id int NOT NULL DEFAULT 0,
    is_first boolean NOT NULL DEFAULT false, PRIMARY KEY(id))');
        $this->addSql('alter sequence kyc_level_id_seq owned by kyc_level.id;');

        $this->addSql('INSERT INTO kyc_level (name, description, advertising_text, parent_level_id, is_first) 
VALUES (\'Start level\', \'This is a start KYC level for new users\', \'\', 0, true)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE kyc_level_id_seq CASCADE');
        $this->addSql('DROP TABLE kyc_level');
    }
}
