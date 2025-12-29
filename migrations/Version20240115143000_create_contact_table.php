<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240115143000_create_contact_table extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create contact table';
    }

    public function up(Schema $schema): void
    {
        // Code pour créer/modifier la base de données
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, ...)');
    }

    public function down(Schema $schema): void
    {
        // Code pour annuler les changements
        $this->addSql('DROP TABLE contact');
    }
}