<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923211348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan CHANGE day day VARCHAR(25) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE role role VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE workout_plan CHANGE day day VARCHAR(25) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan CHANGE day day VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE role role VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE workout_plan CHANGE day day VARCHAR(255) NOT NULL');
    }
}
