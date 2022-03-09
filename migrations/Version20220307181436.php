<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307181436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, competition INT NOT NULL, name VARCHAR(255) NOT NULL, area VARCHAR(255) NOT NULL, last_updated VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B50A2CB1B50A2CB1 (competition), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prediction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, match_id INT NOT NULL, competition INT NOT NULL, match_start_time DATETIME DEFAULT NULL, home_team_score INT DEFAULT NULL, away_team_score INT DEFAULT NULL, home_team_prediction INT NOT NULL, away_team_prediction INT NOT NULL, finished TINYINT(1) NOT NULL, points INT DEFAULT NULL, INDEX IDX_36396FC8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, competition_id INT NOT NULL, name VARCHAR(255) NOT NULL, date_from DATETIME NOT NULL, date_to DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_C5EEEA347B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round_match (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, match_id INT NOT NULL, stage VARCHAR(255) DEFAULT NULL, group_name VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, home_team_name VARCHAR(255) DEFAULT NULL, away_team_name VARCHAR(255) DEFAULT NULL, full_time_home_team_score INT DEFAULT NULL, full_time_away_team_score INT DEFAULT NULL, extra_time_home_team_score INT DEFAULT NULL, extra_time_away_team_score INT DEFAULT NULL, winner VARCHAR(255) DEFAULT NULL, last_updated VARCHAR(255) NOT NULL, INDEX IDX_AC28D1FCA6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', first_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prediction ADD CONSTRAINT FK_36396FC8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA347B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE round_match ADD CONSTRAINT FK_AC28D1FCA6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA347B39D312');
        $this->addSql('ALTER TABLE round_match DROP FOREIGN KEY FK_AC28D1FCA6005CA0');
        $this->addSql('ALTER TABLE prediction DROP FOREIGN KEY FK_36396FC8A76ED395');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE prediction');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE round_match');
        $this->addSql('DROP TABLE user');
    }
}
