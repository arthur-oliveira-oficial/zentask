<?php
// filepath: test_db_connection.php
require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;

// Carrega as variáveis de ambiente do arquivo .env
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

echo "Tentando conectar ao banco de dados usando as configurações do .env...\n";
echo "URL de conexão: " . $_ENV['DATABASE_URL'] . "\n\n";

try {
    // Extrai informações da URL de conexão
    $params = parse_url($_ENV['DATABASE_URL']);

    // Configura os parâmetros de conexão
    $connectionParams = [
        'dbname' => ltrim($params['path'], '/'),
        'user' => $params['user'],
        'password' => $params['pass'],
        'host' => $params['host'],
        'port' => $params['port'] ?? 3306,
        'driver' => 'pdo_mysql',
        'charset' => 'utf8mb4',
    ];

    // Cria a conexão com o banco de dados
    $connection = DriverManager::getConnection($connectionParams);

    // Executa uma consulta que implicitamente abre a conexão
    $result = $connection->executeQuery('SELECT 1')->fetchOne();

    if ($connection->isConnected()) {
        echo "✅ Conexão estabelecida com sucesso!\n";
        echo "Versão do servidor: " . $connection->executeQuery('SELECT version()')->fetchOne() . "\n";
        echo "Banco de dados atual: " . $connection->executeQuery('SELECT DATABASE()')->fetchOne() . "\n\n";

        // Lista todas as tabelas do banco de dados
        echo "📋 Listando todas as tabelas do banco de dados:\n";
        echo "------------------------------------------------\n";

        $tablesResult = $connection->executeQuery('SHOW TABLES')->fetchAllAssociative();

        if (count($tablesResult) > 0) {
            foreach ($tablesResult as $index => $table) {
                $tableName = reset($table); // Obtém o primeiro valor do array (nome da tabela)
                echo ($index + 1) . ". {$tableName}\n";

                // Opcional: mostrar o número de registros de cada tabela
                $rowCount = $connection->executeQuery("SELECT COUNT(*) FROM `{$tableName}`")->fetchOne();
                echo "   → {$rowCount} registro(s)\n";
            }

            echo "\nTotal de tabelas encontradas: " . count($tablesResult) . "\n";
        } else {
            echo "Nenhuma tabela encontrada no banco de dados.\n";
        }
    } else {
        echo "❌ Não foi possível conectar ao banco de dados.\n";
    }

} catch (\Exception $e) {
    echo "❌ Erro ao conectar ao banco de dados: " . $e->getMessage() . "\n";
}
