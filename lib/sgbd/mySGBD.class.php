<?php

/**
 * Classe mySGBD — wrapper PDO que manté la mateixa interfície pública
 * que l'antiga versió basada en PEAR DB.
 *
 * Canvis respecte a la versió anterior:
 *   - PEAR DB eliminat; s'usa PDO (mysql_* van ser eliminades a PHP 7).
 *   - Constructor passat a __construct().
 *   - Classe auxiliar mySGBDResult per mantenir fetchRow() / numRows().
 */

class mySGBDResult
{
    private PDOStatement $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    /** Retorna la següent fila com array associatiu, o null si no n'hi ha més. */
    public function fetchRow(): ?array
    {
        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    /** Número de files afectades/retornades. */
    public function numRows(): int
    {
        return $this->stmt->rowCount();
    }
}


class mySGBD
{
    private array  $dsn;
    private ?PDO   $pdo   = null;
    public  mixed  $lastError = null;

    public function __construct(array $dsn)
    {
        $this->dsn = $dsn;
    }

    // ------------------------------------------------------------------
    // Connexió
    // ------------------------------------------------------------------

    public function connect(): bool
    {
        if ($this->pdo !== null) {
            return true;
        }
        try {
            $this->pdo = new PDO(
                $this->buildPdoDsn(),
                $this->dsn['username'] ?? 'root',
                $this->dsn['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
                ]
            );
            return true;
        } catch (PDOException $e) {
            $this->lastError = $e;
            $this->pdo       = null;
            return false;
        }
    }

    public function reconnect(): bool
    {
        $this->pdo = null;
        return $this->connect();
    }

    public function disconnect(): bool
    {
        $this->pdo = null;
        return true;
    }

    public function close(): bool
    {
        return $this->disconnect();
    }

    public function isConnect(): bool
    {
        return $this->pdo !== null;
    }

    public function getDB(): ?PDO
    {
        return $this->pdo;
    }

    // ------------------------------------------------------------------
    // Consultes
    // ------------------------------------------------------------------

    public function query(string $sql): mySGBDResult|false
    {
        if (!$this->isConnect() && !$this->connect()) {
            return false;
        }
        try {
            $stmt = $this->pdo->query($sql);
            if ($stmt === false) {
                $this->lastError = $this->pdo->errorInfo();
                return false;
            }
            return new mySGBDResult($stmt);
        } catch (PDOException $e) {
            $this->lastError = $e;
            return false;
        }
    }

    public function queryPager(string $sql, int $from = 0, int $limit = 9): mySGBDResult|false
    {
        $sql .= " LIMIT $from, $limit";
        return $this->query($sql);
    }

    public function getArray(string $sql): array|false
    {
        $result = $this->query($sql);
        if ($result === false) {
            return false;
        }
        $rows = [];
        while (($row = $result->fetchRow()) !== null) {
            $rows[] = $row;
        }
        return $rows;
    }

    // ------------------------------------------------------------------
    // Utilitats de taules
    // ------------------------------------------------------------------

    public function getRowsTable(string $tbl_name): int|false
    {
        $result = $this->query("SELECT COUNT(*) AS n FROM `$tbl_name`");
        if ($result === false) {
            return false;
        }
        $row = $result->fetchRow();
        return (int) ($row['n'] ?? 0);
    }

    public function existRecord(string $table, string $field, mixed $value, bool $isString): bool|int
    {
        $table = $this->parseString($table);
        $field = $this->parseString($field);
        $v     = $isString ? "'" . $this->parseString((string)$value) . "'" : (int)$value;

        $result = $this->query("SELECT COUNT(*) AS res FROM `$table` WHERE `$field` = $v");
        if ($result === false) {
            return -1;
        }
        $row = $result->fetchRow();
        return ($row['res'] ?? 0) >= 1;
    }

    /** Retorna informació de columnes d'una taula (format compatible amb l'anterior). */
    public function getInfoTable(mixed $tbl_or_result): array|false
    {
        $table = is_string($tbl_or_result) ? $tbl_or_result : null;
        if ($table === null || (!$this->isConnect() && !$this->connect())) {
            return false;
        }
        try {
            $stmt = $this->pdo->query("DESCRIBE `$table`");
            $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $info = [];
            foreach ($cols as $col) {
                $info[] = [
                    'name'  => $col['Field'],
                    'type'  => $col['Type'],
                    'len'   => 0,
                    'flags' => $col['Key'] === 'PRI' ? 'primary_key' : '',
                ];
            }
            return $info;
        } catch (PDOException $e) {
            $this->lastError = $e;
            return false;
        }
    }

    public function getTablesOfDB(): ?array
    {
        if (!$this->isConnect() && !$this->connect()) {
            return null;
        }
        try {
            $stmt   = $this->pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $tables ?: null;
        } catch (PDOException $e) {
            $this->lastError = $e;
            return null;
        }
    }

    public function valueMax(string $table, string $field): int|false
    {
        $table  = $this->parseString($table);
        $field  = $this->parseString($field);
        $result = $this->query("SELECT MAX(`$field`) AS res FROM `$table`");
        if ($result === false) {
            return false;
        }
        $row = $result->fetchRow();
        return is_null($row['res'] ?? null) ? -1 : (int)$row['res'];
    }

    public function valueMax2(string $table, string $field): int|false
    {
        $table  = $this->parseString($table);
        $field  = $this->parseString($field);
        $result = $this->query("SELECT MAX(`$field`) AS res FROM $table");
        if ($result === false) {
            return false;
        }
        $row = $result->fetchRow();
        return is_null($row['res'] ?? null) ? -1 : (int)$row['res'];
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Escapa un string per usar-lo en queries manuals.
     * Preferir sentències preparades (PDO::prepare) per a codi nou.
     */
    public function parseString(string $value): string
    {
        if ($this->isConnect()) {
            $quoted = $this->pdo->quote($value);
            return substr($quoted, 1, -1);
        }
        return addslashes($value);
    }

    public function isPrimaryKey(array $field): bool
    {
        return str_contains((string)($field['flags'] ?? ''), 'primary_key');
    }

    // ------------------------------------------------------------------
    // Gestió d'errors
    // ------------------------------------------------------------------

    public function getLastError(): mixed
    {
        $e               = $this->lastError;
        $this->lastError = null;
        return $e;
    }

    public function getLastErrorMessage(): string
    {
        if ($this->lastError instanceof PDOException) {
            return $this->lastError->getMessage();
        }
        if (is_array($this->lastError)) {
            return $this->lastError[2] ?? '';
        }
        return (string)($this->lastError ?? '');
    }

    public function getLastErrorCode(): mixed
    {
        if ($this->lastError instanceof PDOException) {
            return $this->lastError->getCode();
        }
        if (is_array($this->lastError)) {
            return $this->lastError[1] ?? '';
        }
        return '';
    }

    public function getLastErrorUserinfo(): string
    {
        if ($this->lastError instanceof PDOException) {
            return $this->lastError->getMessage();
        }
        return '';
    }

    // ------------------------------------------------------------------
    // Privat
    // ------------------------------------------------------------------

    private function buildPdoDsn(): string
    {
        $host = $this->dsn['hostspec'] ?? 'localhost';
        $port = '';
        if (str_contains($host, ':')) {
            [$host, $portNum] = explode(':', $host, 2);
            $port = ";port=$portNum";
        }
        $database = $this->dsn['database'] ?? '';
        return "mysql:host=$host$port;dbname=$database;charset=utf8mb4";
    }
}
