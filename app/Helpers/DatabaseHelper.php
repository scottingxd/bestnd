<?php
namespace App\Helpers;

use PDO;
use PDOException;
use PDOStatement;

class DatabaseHelper
{
    private static ?PDO $connection = null;
    
    private function __construct() {}
    
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
                ]);
                
            } catch (PDOException $e) {
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    throw new PDOException('Erro de conexão: ' . $e->getMessage());
                } else {
                    throw new PDOException('Erro ao conectar com o banco de dados.');
                }
            }
        }
        
        return self::$connection;
    }
    
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int) self::getConnection()->lastInsertId();
    }
    
    public static function update(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    public static function delete(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
}