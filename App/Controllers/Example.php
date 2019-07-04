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

     public function test(){
         $model = new Tests();
         
         foreach($model->findAll() as $test){
             echo $test->get_Example()->Name; // get_EntityName() get related table data
         }
     }
}