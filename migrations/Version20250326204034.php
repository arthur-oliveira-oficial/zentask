<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326204034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrador (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, status VARCHAR(20) DEFAULT \'pendente\' NOT NULL, ultimo_login_em DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_44F9A521E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE empresas (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, host_db VARCHAR(255) NOT NULL, usuario_db VARCHAR(255) NOT NULL, senha_db VARCHAR(255) NOT NULL, status VARCHAR(20) DEFAULT \'pendente\' NOT NULL, data_de_expiracao_plano DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, plano_de_pagamento_id INT NOT NULL, INDEX IDX_70DD49A551DB1D98 (plano_de_pagamento_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE historico_de_pagamentos (id INT AUTO_INCREMENT NOT NULL, data_pagamento DATE NOT NULL, valor_pago NUMERIC(10, 2) NOT NULL, metodo_pagamento VARCHAR(50) NOT NULL, status_pagamento VARCHAR(20) DEFAULT \'pendente\' NOT NULL, transacao_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, empresa_id INT NOT NULL, plano_de_pagamento_id INT NOT NULL, INDEX IDX_53BFA696521E1991 (empresa_id), INDEX IDX_53BFA69651DB1D98 (plano_de_pagamento_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE planos_de_pagamento (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, descricao LONGTEXT DEFAULT NULL, preco NUMERIC(10, 2) NOT NULL, periodicidade VARCHAR(20) NOT NULL, recursos LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_80E823B654BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE empresas ADD CONSTRAINT FK_70DD49A551DB1D98 FOREIGN KEY (plano_de_pagamento_id) REFERENCES planos_de_pagamento (id)');
        $this->addSql('ALTER TABLE historico_de_pagamentos ADD CONSTRAINT FK_53BFA696521E1991 FOREIGN KEY (empresa_id) REFERENCES empresas (id)');
        $this->addSql('ALTER TABLE historico_de_pagamentos ADD CONSTRAINT FK_53BFA69651DB1D98 FOREIGN KEY (plano_de_pagamento_id) REFERENCES planos_de_pagamento (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE empresas DROP FOREIGN KEY FK_70DD49A551DB1D98');
        $this->addSql('ALTER TABLE historico_de_pagamentos DROP FOREIGN KEY FK_53BFA696521E1991');
        $this->addSql('ALTER TABLE historico_de_pagamentos DROP FOREIGN KEY FK_53BFA69651DB1D98');
        $this->addSql('DROP TABLE administrador');
        $this->addSql('DROP TABLE empresas');
        $this->addSql('DROP TABLE historico_de_pagamentos');
        $this->addSql('DROP TABLE planos_de_pagamento');
    }
}
