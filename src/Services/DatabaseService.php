<?php

/**
 * Database access service.
 *
 * No active records, cursors or other stuff.  Just a PDO wrapper.
 **/

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOStatement;

class DatabaseService
{
    /**
     * @var array
     **/
    protected $settings;

    /**
     * @var PDO
     **/
    protected $conn;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->conn = null;
    }

    public function beginTransaction(): void
    {
        $this->connect()->beginTransaction();
    }

    public function commit(): void
    {
        $this->connect()->commit();
    }

    public function fetch(string $query, array $params = array(), $callback = null): array
    {
        $db = $this->connect();
        $sth = $db->prepare($query);
        $sth->execute($params);

        $res = $sth->fetchAll(PDO::FETCH_ASSOC);

        if ($callback) {
            $res = array_filter(array_map($callback, $res));
        }

        return $res;
    }

    public function fetchKV(string $query, array $params = []): array
    {
        $rows = $this->fetch($query, $params);

        $res = [];
        foreach ($rows as $row) {
            $row = array_values($row);
                $res[$row[0]] = $row[1];
        }

        return $res;
    }

    public function fetchOne(string $query, array $params = array()): ?array
    {
        $db = $this->connect();
        $sth = $db->prepare($query);
        $sth->execute($params);
        return $sth->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function fetchCell(string $query, array $params = array())
    {
        $db = $this->connect();
        $sth = $db->prepare($query);
        $sth->execute($params);

        return $sth->fetchColumn(0);
    }

    public function insert(string $tableName, array $fields): ?int
    {
        $_fields = [];
        $_marks = [];
        $_params = [];

        foreach ($fields as $k => $v) {
            $_fields[] = "`{$k}`";
            $_params[] = $v;
            $_marks[] = "?";
        }

        $_fields = implode(", ", $_fields);
        $_marks = implode(", ", $_marks);

        $query = "INSERT INTO `{$tableName}` ({$_fields}) VALUES ({$_marks})";
        $sth = $this->query($query, $_params);

        return (int)$this->conn->lastInsertId();
    }

    public function prepare(string $query): PDOStatement
    {
        return $this->connect()->prepare($query);
    }

    public function query(string $query, array $params = []): PDOStatement
    {
        try {
            $db = $this->connect();
            $sth = $db->prepare($query);
            $sth->execute($params);
            return $sth;
        } catch (PDOException $e) {
            $_m = $e->getMessage();

            // Server gone away.
            if ($_m == 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away') {
                $this->conn = $this->connect();
                return $this->query($query, $params);
            }

            if ($_m = "SQLSTATE[HY000]: General error: 8 attempt to write a readonly database") {
                if (0 === strpos($this->dsn["name"], "sqlite:")) {
                    $fn = substr($this->dsn["name"], 7);
                    if (!is_writable($fn)) {
                        throw new \RuntimeException("SQLite database is not writable.");
                    } elseif (!is_writable(dirname($fn))) {
                        throw new \RuntimeException("SQLite database folder is not writable.");
                    }
                }
            }
            throw $e;
        }
    }

    public function getConnectionType(): string
    {
        $this->connect();
        return $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function rollback(): void
    {
        $this->connect()->rollback();
    }

    public function update(string $tableName, array $fields, array $where): int
    {
        $_set = [];
        $_where = [];
        $_params = [];

        foreach ($fields as $k => $v) {
            $_set[] = "`{$k}` = ?";
            $_params[] = $v;
        }

        foreach ($where as $k => $v) {
            $_where[] = "`{$k}` = ?";
            $_params[] = $v;
        }

        $_set = implode(", ", $_set);

        $query = "UPDATE `{$tableName}` SET {$_set}";

        if (!empty($_where)) {
            $_where = implode(" AND ", $_where);
            $query .= " WHERE {$_where}";
        }

        $sth = $this->query($query, $_params);
        return $sth->rowCount();
    }

    /**
     * Connect to the database.
     *
     * @return PDO Database connection.
     **/
    protected function connect(): PDO
    {
        if (null === $this->conn) {
            $dsn = $this->settings['dsn'] ?? null;
            $user = $this->settings['user'] ?? null;
            $pass = $this->settings['password'] ?? null;

            if (null === $dsn) {
                throw new \RuntimeException("database not configured");
            }

            $this->conn = new PDO($dsn, $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            if (0 === strpos($dsn, 'mysql:')) {
                $this->conn->query('SET NAMES utf8');
            }

            // Perform initialization stuff, like SET NAMES utf8.
            if (!empty($this->settings['bootstrap'])) {
                foreach ($this->settings['bootstrap'] as $query) {
                    $this->conn->query($query);
                }
            }
        }

        return $this->conn;
    }
}
