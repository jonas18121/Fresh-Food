<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427142105 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1F675F31B ON category (author_id)');
        $this->addSql('ALTER TABLE emplacement CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE emplacement ADD CONSTRAINT FK_C0CF65F6F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0CF65F6F675F31B ON emplacement (author_id)');
        $this->addSql('ALTER TABLE product CHANGE classified_in_id classified_in_id INT NOT NULL, CHANGE place_in_id place_in_id INT NOT NULL');
        $this->addSql('ALTER TABLE unity CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE unity ADD CONSTRAINT FK_9659D57F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9659D57F675F31B ON unity (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1F675F31B');
        $this->addSql('DROP INDEX IDX_64C19C1F675F31B ON category');
        $this->addSql('ALTER TABLE emplacement DROP FOREIGN KEY FK_C0CF65F6F675F31B');
        $this->addSql('DROP INDEX IDX_C0CF65F6F675F31B ON emplacement');
        $this->addSql('ALTER TABLE emplacement CHANGE author_id author_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product CHANGE classified_in_id classified_in_id INT DEFAULT NULL, CHANGE place_in_id place_in_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unity DROP FOREIGN KEY FK_9659D57F675F31B');
        $this->addSql('DROP INDEX IDX_9659D57F675F31B ON unity');
        $this->addSql('ALTER TABLE unity CHANGE author_id author_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
