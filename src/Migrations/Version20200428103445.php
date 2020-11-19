<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200428103445 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employment (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, movie_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_BF089C98217BBB47 (person_id), INDEX IDX_BF089C988F93B6FC (movie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FBD8E0F8AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor (id INT NOT NULL, role VARCHAR(255) NOT NULL, credit_order INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crew_member (id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_F4D78A97BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE director (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employment ADD CONSTRAINT FK_BF089C98217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE employment ADD CONSTRAINT FK_BF089C988F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE actor ADD CONSTRAINT FK_447556F9BF396750 FOREIGN KEY (id) REFERENCES employment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE crew_member ADD CONSTRAINT FK_F4D78A97BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE crew_member ADD CONSTRAINT FK_F4D78A97BF396750 FOREIGN KEY (id) REFERENCES employment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE director ADD CONSTRAINT FK_1E90D3F0BF396750 FOREIGN KEY (id) REFERENCES employment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE actor DROP FOREIGN KEY FK_447556F9BF396750');
        $this->addSql('ALTER TABLE crew_member DROP FOREIGN KEY FK_F4D78A97BF396750');
        $this->addSql('ALTER TABLE director DROP FOREIGN KEY FK_1E90D3F0BF396750');
        $this->addSql('ALTER TABLE crew_member DROP FOREIGN KEY FK_F4D78A97BE04EA9');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8AE80F5DF');
        $this->addSql('DROP TABLE employment');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE crew_member');
        $this->addSql('DROP TABLE director');
    }
}
