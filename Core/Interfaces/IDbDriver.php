<?php 
namespace Core\Interfaces;

interface IDbDriver {
    
    public function getConnection();
    public function query($sql);
    public function multiQuery($sql);
    public function fetch();
    public function fetchObject();
    public function getAll($sql);
    public function getOne($sql);
    public function getStatement();

    public function getFields($table);
    public function pk();
    public function getInsertId();
    public function errno();
    public function error();
    public function close();

    public function beginTransaction();
    public function commit();
    public function rollback();
}