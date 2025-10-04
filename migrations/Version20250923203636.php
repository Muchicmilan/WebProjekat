<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923203636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meal (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, protein_g NUMERIC(5, 2) NOT NULL, carbs_g NUMERIC(5, 2) NOT NULL, calories_kcal NUMERIC(6, 2) NOT NULL, fat_g NUMERIC(5, 2) NOT NULL, recipe LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_plan (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meal_plan_meal (meal_plan_id INT NOT NULL, meal_id INT NOT NULL, INDEX IDX_354F4065912AB082 (meal_plan_id), INDEX IDX_354F4065639666D6 (meal_id), PRIMARY KEY(meal_plan_id, meal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_request (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, date_sent DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', time_sent TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', content LONGTEXT NOT NULL, INDEX IDX_AB1FB593F624B39D (sender_id), INDEX IDX_AB1FB593CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, notes LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, surname VARCHAR(40) NOT NULL, email VARCHAR(70) NOT NULL, password_hash VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, height NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_plan (user_id INT NOT NULL, plan_id INT NOT NULL, INDEX IDX_A7DB17B4A76ED395 (user_id), INDEX IDX_A7DB17B4E899029B (plan_id), PRIMARY KEY(user_id, plan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_progress (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, weight_kg NUMERIC(5, 2) NOT NULL, date DATETIME NOT NULL, INDEX IDX_C28C1646A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout (id INT AUTO_INCREMENT NOT NULL, sets INT NOT NULL, reps INT NOT NULL, name VARCHAR(50) NOT NULL, notes LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_plan (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workout_plan_workout (workout_plan_id INT NOT NULL, workout_id INT NOT NULL, INDEX IDX_FD2FF39E945F6E33 (workout_plan_id), INDEX IDX_FD2FF39EA6CCCFC9 (workout_id), PRIMARY KEY(workout_plan_id, workout_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meal_plan_meal ADD CONSTRAINT FK_354F4065912AB082 FOREIGN KEY (meal_plan_id) REFERENCES meal_plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_plan_meal ADD CONSTRAINT FK_354F4065639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_request ADD CONSTRAINT FK_AB1FB593F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message_request ADD CONSTRAINT FK_AB1FB593CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_plan ADD CONSTRAINT FK_A7DB17B4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_plan ADD CONSTRAINT FK_A7DB17B4E899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_progress ADD CONSTRAINT FK_C28C1646A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE workout_plan_workout ADD CONSTRAINT FK_FD2FF39E945F6E33 FOREIGN KEY (workout_plan_id) REFERENCES workout_plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE workout_plan_workout ADD CONSTRAINT FK_FD2FF39EA6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workout (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal_plan_meal DROP FOREIGN KEY FK_354F4065912AB082');
        $this->addSql('ALTER TABLE meal_plan_meal DROP FOREIGN KEY FK_354F4065639666D6');
        $this->addSql('ALTER TABLE message_request DROP FOREIGN KEY FK_AB1FB593F624B39D');
        $this->addSql('ALTER TABLE message_request DROP FOREIGN KEY FK_AB1FB593CD53EDB6');
        $this->addSql('ALTER TABLE user_plan DROP FOREIGN KEY FK_A7DB17B4A76ED395');
        $this->addSql('ALTER TABLE user_plan DROP FOREIGN KEY FK_A7DB17B4E899029B');
        $this->addSql('ALTER TABLE user_progress DROP FOREIGN KEY FK_C28C1646A76ED395');
        $this->addSql('ALTER TABLE workout_plan_workout DROP FOREIGN KEY FK_FD2FF39E945F6E33');
        $this->addSql('ALTER TABLE workout_plan_workout DROP FOREIGN KEY FK_FD2FF39EA6CCCFC9');
        $this->addSql('DROP TABLE meal');
        $this->addSql('DROP TABLE meal_plan');
        $this->addSql('DROP TABLE meal_plan_meal');
        $this->addSql('DROP TABLE message_request');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_plan');
        $this->addSql('DROP TABLE user_progress');
        $this->addSql('DROP TABLE workout');
        $this->addSql('DROP TABLE workout_plan');
        $this->addSql('DROP TABLE workout_plan_workout');
    }
}
