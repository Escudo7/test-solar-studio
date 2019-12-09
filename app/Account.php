<?php

namespace App;

class Account
{
    protected static $pdo;
    protected static $db = 'bank';
    protected static $table = 'accounts';

    public $id;

    public $fields = [
        'name' => null,
        'surname' => null,
        'patronymic' => null,
        'birthday' => null,
        'account' => null,
        'amount' => null,
    ];
    
    public static function setConnection($pdo)
    {
        self::$pdo = $pdo;
        $db = self::$db;
        $table = self::$table;
        $creatingTable = "CREATE TABLE IF NOT EXISTS {$table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(128) NOT NULL,
            surname VARCHAR(128) NOT NULL,
            patronymic VARCHAR(128),
            birthday DATE,
            account BIGINT,
            amount BIGINT)";
        mysqli_query($pdo, $creatingTable);
    }

    public static function getAccounts($limit, $offset)
    {
        $table = self::$table;
        $query = "SELECT * FROM {$table} ORDER BY id LIMIT {$limit} OFFSET {$offset}";
        return mysqli_query(self::$pdo, $query)->fetch_all(MYSQLI_ASSOC);
    }

    public static function count()
    {
        $table = self::$table;
        $query = "SELECT id FROM {$table}";
        return mysqli_query(self::$pdo, $query)->num_rows;
    }

    public function save()
    {
        $table = self::$table;
        if (!isset($this->id)) {
            $data = array_reduce(array_keys($this->fields), function ($acc, $key) {
                if (!empty($this->fields[$key])) {
                    $acc['columns'][] = $key;
                    $value = $this->fields[$key];
                    $acc['values'][] = "'{$value}'";
                }
                return $acc;
            }, ['columns' => [], 'values' => []]);
            $columns = implode(', ', $data['columns']);
            $values = implode(', ', $data['values']);
            $query = "INSERT INTO {$table}({$columns}) VALUES ({$values})";
            mysqli_query(self::$pdo, $query);
            $this->id = mysqli_insert_id(self::$pdo);
        } else {
            $data = array_reduce(array_keys($this->fields), function ($acc, $key) {
                if (!empty($this->fields[$key])) {
                    $value = $this->fields[$key];
                    $acc[] = "{$key} = '{$value}'";
                }
                return $acc;
            }, []);
            $update = implode(', ', $data);
            $query = "UPDATE {$table} SET {$update} WHERE id = {$this->id}";
            mysqli_query(self::$pdo, $query);
        }
    }

    public function getData()
    {
        return $this->fields;
    }

    public function __set($field, $value)
    {
        if (array_key_exists($field, $this->fields)) {
            $this->fields[$field] = $value;
        }
    }

    public function __get($field)
    {
        return $field == 'id' ? $this->id : $this->fields[$field];
    }
}
