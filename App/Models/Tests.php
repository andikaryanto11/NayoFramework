<?php
namespace App\Models;
use Core\Nayo_Model;

class Tests extends Nayo_Model {
    /**
     * table name must be in plural
     * define all your fields table here
     * i give some query table creation in App\Database\Migrations
     */

    public $Id; // should exist in every table and must int and autoincrement
    public $Example_Id;
    public $Name;

    /**
     * $table set as name of table
     * $entity set as name of table with first upper case letter
     * 
     * if your table named like m_example
     * it would be
     * $table = m_example
     */

    /**
     * it's ORM look like base
     * so you have to name your table as example
     * 
     * case : if you have 2 tables related it should be like this
     * table 1 : m_groupusers(Id, name, ....)
     * table 2 : m_users(Id, M_Groupuser_Id,....)
     * foreign key should be Example_Id , ex : M_Groupuser_Id
     */

    protected $table = 'tests';
    

}