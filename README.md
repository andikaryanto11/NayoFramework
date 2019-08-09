# Nayo Framework
Nayo Framework is php MVC base, built from native php
insipred by Code Igniter and Entity framework (.Net EDMX)

MAIN PURPOSE why i build this app is to share code MVC, 
if someone wants to know how php MVC works.
It's simple code , and could be modified by everyone. 

# Installation

0. install composer for php
1. copy app to your web server
2. go to Framework folder from cmd/console. type "composer update" 

# Simple Guide
 it's Code igniter inspired App. mostly look like Code Igniter for config (App\Config)

 Main difference from code igniter is in the model (ORM look like) and migration
 so i give simple explaination for Modeling
# 0. Config 
App\Config\Databse.php
Support 2 database driver mysql and sqlsrv php (for sql server > 2008, ex: 2012 or greater)
see for php setup to ODBC driver 

    'connectionstring' => "",
    'host' => '', // host of database ex : 192.168.1.xxx or .\MSSQLSERVER
    'user' => '',
    'password' => '',
    'dbname' => '',
    'driver' => '.mysqli', //.mysqli, .sqlsrv
    'port' => '',
    'charset' => '',
    'engine' => ''
 
# 1. Models
Model class must extends Nayo_Model class
    
* table name must be in plural
* file name must be like table name, ex : Examples.php , upper case first letter
* class name must be like table name, ex : Examples , upper case first letter
* table must have field Id (int, auto increment)
* i give some query table creation in App\Database\Migrations
   
   
Here example :
  
    <?php
    namespace App\Models;
    use Core\Nayo_Model;

    class Examples extends Nayo_Model {
      /**
       * define all your fields table here
       */
       
      public $Id;
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

      protected $table = 'examples';
    }
    
    
 # Methods

 if model class extends Nayo_Model class, class wil have CRUD method

 * modelclass->query($sql);
 * modelclass::get($id) 

  example :
  
      Example::get(1);
      //this method wiil return result table with id = 1
      // Example is the object class that is mapped to table;
      
 * modelclass->save()

  if the object Id(field) is set, ex : Id = 1
  this method will update the data, otherwise will save new data
  example: 
  
      //save data
      $model = new Example();
      $model->Name = 'new Name';
      $model->save();
      
      //update data
      $model = Example::get(1);
      $model->Name = 'edited Name';
      $model->save();
      
 * modelclass->delete()
  
  example: 
  
      $model = Example::get(1);
      $model->delete();
      
      //this method will delete data with Id = 1
      
  * modelclass::getAll($array)
    $array is optional
    example: 
        
        $params = array(
            'where' => array(
                'Name' => 'existname'
            )
        );
        
        //filtered
        $result = Example::getAll($params);
        
        //or get all data
        $result = Example::getAll();
        
  * modelclass->get_Entity()
   Entity is ojbect table related name with upper case first letter
   
   method will get related data with many to 1 relation
   
   example: 
          
         //Tests is a model class object
         foreach(Tests::getAll() as $test){
             echo $test->get_Example()->Name; // get_EntityName() get related table data
         }
         
  * modelclass->get_list_Entity()
   Entity is ojbect table related name with upper case first letter
   method will get related data with 1 to many relation
   
   example: 
          
         $data = Example::get(1);
         foreach($data->get_list_Test() as $test){
             
         }
        
# 2. Controllers
  Controller class must extends Nayo_Controller class
  
  Here example :
  
    <?php
    namespace App\Controllers;
    use Core\Nayo_Controller;
    use App\Models\Tests;

    class Example extends Nayo_Controller{

      public function __construct(){
          parent::__construct();

      }

      public function index(){
          $this->view('example/index');
      }
      
      public function load_data(){
          $data['model'];
          $this->view('example/index', $data);
      }

      /**
       * using model 
       * 
       * you can read all function in Core\Model
       */
       public function test(){

           foreach(Tests::findAll() as $test){
               echo $test->get_Example()->Name; // get_EntityName() get related table data
           }
       }
    }
    
# Views
  you can see the controllers, how to load the view and pass the data
  
# Routes
see Nezamy route for more info at https://nezamy.com/Route/

# CLI
 CLI is used to generate CONTROLLERS, MODELS, MIGRATION, and to migrate all migration
  # Help
  is used to see what command lines available are
   
    php index.php help
    
  # Controllers
  will generate controller file in App\Controllers
  
    php index.php controller controller_name 
    
  # Model 
  will generate model file in App\Models, it must have parameter that relate on table name, case sensitive,
  and it will have all property from your table
   
    php index.php model table_name
  
  # Migration
  will generate migration file in App\Database\Migrations that has file name as current time, ex : Migration_yyyymmddhhiiss.php
  
    php index.php migration
    
    <?php
    namespace App\Database\Migrations;
    use Core\Database\Table;

    class migration_20190620045801 {

        public function up(){
            
            //creating table
            $table = new Table();
            $table->table('m_companies');
            $table->addColumn("Id", "int", "11", false, null, true, true);
            $table->addColumn("CompanyName", "Varchar", "50");
            $table->addColumn("Address", "Varchar", "300");
            $table->addColumn("PostCode", "Varchar", "10");
            $table->addColumn("Email", "Varchar", "300");
            $table->addColumn("Phone", "Varchar", "50");
            $table->addColumn("Fax", "Varchar", "50", true);
            $table->addColumn("UrlPhoto", "Varchar", "500", true);
            $table->addColumn("CreatedBy", "Varchar", "50", true);
            $table->addColumn("ModifiedBy", "Varchar", "50", true);
            $table->addColumn("Created", "datetime", "", true);
            $table->addColumn("Modified", "datetime", "", true);
            $table->create();
        }
    }
    
  # Migrate
  will migrate all migrations that haven't been in your database migration
    
    php index.php migrate 
   
# Datatables
server side proses datatables will return json format to

    <?php
    namespace App\Controllers;
    use App\Models\Example;
    use Core\Libraries\Datatables;

    class M_foodcategory extends Base_Controller{
        public function index(){
            $params = [
                'where' => [
                    'Name !' => 'a'
                ]
            ];
            
            $datatable = new Datatables('M_Examples', $params); // $params are optional
            $datatable
            ->addColumn(
                'Id', 
                function($row){
                    return $row->Id;
                },
                false,
                false
            )->addColumn(
                'Name', 
                function($row){
                    return "<td>".
                        formLink($row->Name, array("id" => $row->Id,
                                                    "href" => baseUrl('mfoodcategory/edit/'.$row->Id),
                                                    "class" => "text-muted"))
                    ."</td>";
                }
            )->addColumn(
                'Description', 
                function($row){
                    return $row->Description;
                }
            )->addColumn(
                'Created', 
                function($row){
                    return $row->Created;
                },
                false
            )->addColumn(
                'Action', 
                function($row){
                    return "<td class = 'td-actions text-right'>".
                        formLink("<i class='fa fa-trash'>", array(
                                            "href" => "#",
                                            "class" => "btn-just-icon link-action delete",
                                            "rel" => "tooltip",
                                            "title" => lang('Form.delete')))
                    ."</td>";
                },
                false,
                false
            );

            echo json_encode($datatable->populate());
        }
    }
