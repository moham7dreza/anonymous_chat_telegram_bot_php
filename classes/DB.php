<?php

namespace classes;

use PDOException;


class DB
{
    private $tableName;
    private $sql = "";
    private $values = [];
    private $wheres = "";
    private $orderByProperty = "";
    private $allowMethods = ['table'];

    public function table($table)
    {
        if (in_array('table', $this->allowMethods)) {
            $this->tableName = $table;
            $this->allowMethods = ['insert', 'where', 'orWhere', 'orderBy', 'find', 'first', 'get'];
            return $this;
        }

        set_log('DATABASE', 'use "table" function incorrect');

        return false;
    }
    public function find($id)
    {
        if (in_array('select', $this->allowMethods)) {

            $this->sql = "SELECT * FROM `" . $this->tableName . "`";
            $this->wheres = "id = ?";
            $this->values[] = $id;
            $result = $this->runSql();
            if ($result) {
                return $result->fetch();
            }
            return false;

        }
        set_log('DATABASE', 'use "find" function incorrect');

        return false;
    }
    private function select($columns = '*')
    {
            if (is_array($columns)) {
                $columns = implode(', ', $columns);
            }
            $this->sql = "SELECT " . $columns . " FROM `" . $this->tableName . "`";
    }
    public function where($column, $condition, $value = null)
    {
        if (in_array('where', $this->allowMethods)) {

            $startWhere = $this->wheres != "" ? " AND " : "";

            if ($value == null) {

                $this->wheres .=  $startWhere . $column . " = ? ";
                $this->values[] = $condition;
            } else {
                $this->wheres .=  $startWhere . $column . " $condition ?";
                $this->values[] = $value;
            }
            $this->allowMethods = ['where', 'orWhere', 'orderBy', 'get', 'first', 'update', 'delete'];

            return $this;
        }
        set_log('DATABASE', 'use "where" function incorrect');

        return false;
    }

    public function orWhere($column, $condition, $value = null)
    {
        if (in_array('orWhere', $this->allowMethods)) {

            $startWhere = $this->wheres != "" ? " OR " : "";

            if ($value == null) {

                $this->wheres .=  $startWhere . $column . " = ? ";
                $this->values[] = $condition;
            } else {
                $this->wheres .=  $startWhere . $column . " $condition ?";
                $this->values[] = $value;
            }
            $this->allowMethods = ['where', 'orWhere', 'orderBy', 'get', 'first', 'update', 'delete'];

            return $this;
        }
        set_log('DATABASE', 'use "whereOr" function incorrect');

        return false;
    }

    public function orderBy($column, $asc = 'asc')
    {
        if (in_array('orderBy', $this->allowMethods)) {
            $this->orderByProperty = ' ORDER BY ' . $column . " " . $asc;
            $this->allowMethods = ['get', 'first'];

            return $this;
        }
        set_log('DATABASE', 'use "orderBy" function incorrect');

        return false;
    }
    public function first($columns = '*')
    {
        if (in_array('first', $this->allowMethods)) {
            $this->select($columns);
            $result = $this->runSql();
            if ($result) {
                return $result->fetch();
            }
            return false;
        }
        set_log('DATABASE', 'use "first" function incorrect');

        return false;
    }

    public function get($columns = '*')
    {
        if (in_array('get', $this->allowMethods)) {
            $this->select($columns);

            $result = $this->runSql();
            if ($result) {
                return $result->fetchAll();
            }
            return false;
        }
        set_log('DATABASE', 'use "get" function incorrect');

        return false;
    }

    public function insert($fields, $values)
    {
        if (in_array('insert', $this->allowMethods)) {
            $this->sql = "INSERT INTO " . $this->tableName . " (" . implode(', ', $fields) . " , created_at) VALUES ( :" . implode(", :", $fields) . ", now() );";
            $this->values = array_combine($fields, $values);
            $result = $this->runSql();

            if ($result) {
                return true;
            }
            return false;
        }
        set_log('DATABASE', 'use "insert" function incorrect');

        return false;
    }

    public function update($fields, $values)
    {
        if (in_array('update', $this->allowMethods) and $this->wheres != "") {
            $this->sql = "UPDATE `" . $this->tableName . "` SET";
            $values_new = [];

            if (is_array($fields) and is_array($values)) {

                foreach (array_combine($fields, $values) as $field => $value) {
                    if ($value) {
                        $this->sql .= " `" . $field . "`= ? ,";
                        $values_new[] = $value;
                    } else if ($value === 0)
                        $this->sql .= " `" . $field . "`= 0,";
                    else if ($value === false)
                        $this->sql .= " `" . $field . "`= false,";
                    else
                        $this->sql .= " `" . $field . "`= NULL,";
                }
            } else {
                $this->sql .= " `" . $fields . "`= ? ,";
                $values_new[] = $values;
            }
            $this->sql .= " `updated_at`= now() ";
            $this->values = array_merge($values_new, $this->values);

            $result  = $this->runSql();

            if ($result)
                return true;


            return false;
        }
        set_log('DATABASE', 'use "update" function incorrect');

        return false;
    }

    public function delete()
    {
        if (in_array('delete', $this->allowMethods) and $this->wheres != "") {

            $this->sql = "DELETE FROM " . $this->tableName . " ";
            $result = $this->runSql();

            if ($result) {
                return true;
            }
            return false;

        }
        set_log('DATABASE', 'use "delete" function incorrect');

        return false;
    }


    public function runSql()
    {
        try {
            $instance = DBConnection::getInstance();
            if ($instance) {
                if (!empty($this->values)) {
                        $fullSql = $this->sql . " WHERE " . $this->wheres . " " . $this->orderByProperty;
                        $stmt = $instance->prepare($fullSql);
                        $stmt->execute($this->values);    
                } else {
                    $fullSql = $this->sql . $this->orderByProperty;
                    $stmt = $instance->query($fullSql);
                }
                $this->default();

                return $stmt;
            }
            set_log('DATABASE', 'instance is null');
            return false;
        } catch (PDOException $e) {
            set_log('DATABASE', $e->getMessage());
            return false;
        }
    }

    public function default()
    {
        $this->tableName = "";
        $this->sql = "";
        $this->wheres = "";
        $this->orderByProperty = "";
        $this->values = [];
        $this->allowMethods = ['table'];
    }
}

