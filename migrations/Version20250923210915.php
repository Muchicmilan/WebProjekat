<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923210915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan ADD plan_id INT NOT NULL');
        $this->addSql('ALTER TABLE meal_plan ADD CONSTRAINT FK_C7848889E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_C7848889E899029B ON meal_plan (plan_id)');
        $this->addSql('ALTER TABLE workout_plan ADD plan_id INT NOT NULL');
        $this->addSql('ALTER TABLE workout_plan ADD CONSTRAINT FK_A5D45801E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_A5D45801E899029B ON workout_plan (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan DROP FOREIGN KEY FK_C7848889E899029B');
        $this->addSql('DROP INDEX IDX_C7848889E899029B ON meal_plan');
        $this->addSql('ALTER TABLE meal_plan DROP plan_id');
        $this->addSql('ALTER TABLE workout_plan DROP FOREIGN KEY FK_A5D45801E899029B');
        $this->addSql('DROP INDEX IDX_A5D45801E899029B ON workout_plan');
        $this->addSql('ALTER TABLE workout_plan DROP plan_id');
    }
}
