<?php

namespace Livro\Database;

use Exception;

/**
 * Cria conexões com bancos de dados
 * @author André Moura
 */
final class Connection
{
    /**
     * Não podem existir instâncias de TConnection
     */
    private function __construct ( ) 
    {

    }
    
    /**
     * Recebe o nome do conector de BD e instancia o objeto PDO
     */
    public static function open ( $name )
    {
        // verifica se existe arquivo de configuração para este banco de dados
        if ( file_exists ( "App/config/{$name}.ini" ) ) {
            // lê o INI e retorna um array
            $db = parse_ini_file ( "App/config/{$name}.ini" );
        } else if ( file_exists ( "App/config/{$name}.php" ) ) {
            $db = require "App/config/{$name}.php";
        } else {
            // se não existir, lança um erro
            throw new Exception ( "Arquivo '$name' não encontrado" );
        }
        
        // lê as informações contidas no arquivo
        $user = isset($db['user']) ? $db['user'] : null;
        $pass = isset($db['pass']) ? $db['pass'] : null;
        $name = isset($db['name']) ? $db['name'] : null;
        $host = isset($db['host']) ? $db['host'] : null;
        $type = isset($db['type']) ? $db['type'] : null;
        $port = isset($db['port']) ? $db['port'] : null;
        
        // descobre qual o tipo (driver) de banco de dados a ser utilizado
        switch ($type) {
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new \PDO ( "pgsql:dbname={$name}; user={$user}; password={$pass};
                        host=$host;port={$port}" );
                    break;

            case 'mysql':
                $port = $port ? $port : '3306';
                $conn = new \PDO ( "mysql:host={$host};port={$port};dbname={$name};charset=utf8", $user, $pass );
                    break;

            case 'sqlite':
                $conn = new \PDO("sqlite:App/Database/{$name}.db");
                $conn->query('PRAGMA foreign_keys = ON');
                break;

            case 'ibase':
                $conn = new \PDO ( "firebird:dbname={$name}", $user, $pass );
                    break;

            case 'oci8':
                $conn = new \PDO ( "oci:dbname={$name}", $user, $pass );
                    break;

            case 'mssql':
                $conn = new \PDO ( "dblib:host={$host}:{$port};dbname={$name}", $user, $pass );
                    break;
        }

        // define para que o PDO lance exceções na ocorrência de erros
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
