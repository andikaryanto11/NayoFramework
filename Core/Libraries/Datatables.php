<?php
namespace Core\Libraries;

use Core\Request;

class Datatables {

    protected $entity = false;
    protected $filter = false;
    protected $request = false;
    protected $dtRowClass;
    protected $dtRowId;
    protected $columnCounter = 0;
    protected $column = array();

    /**
     * Class constructor.
     */
    public function __construct($entity, $filter = array())
    {
        if(!$this->entity){
            $this->entity = $entity;
        }

        if(!empty($filter))
            $this->filter = $filter;
        
        if(!$this->request)
            $this->request = Request::getInstance();
    }

    private function newEntity(){
        $ent = 'App\\Models\\'.$this->entity;
        return $ent;
    }

    public function populate(){

        $model = $this->newEntity();
        $params = array(
            'where' => isset($this->filter['where']) ? $this->filter['where'] : null

        );

        if($this->request->get('length') != -1){
            $params['limit'] = array(
                'page' => $this->request->get('start') + 1,
                'size' => $this->request->get('length')
            );
        }

        if($this->request->get('search') && $this->request->get('search')['value'] != ''){
            $searchValue = $this->request->get('search')['value'];

            foreach($this->column as $column){
                if($column['searchable']){
                    $params['group']['orlike'][$column['column']] = $searchValue;
                }
            }
        }

        if($this->request->get('order') && count($this->request->get('order'))){
            $order = $this->request->get('order')[0];
            
            if(isset($this->column[$order['column']]) && $this->column[$order['column']]['orderable'])
                $params['order'] = array(
                    $this->column[$order['column']]['column'] =>  $order['dir'] === 'asc' ? "ASC" : "DESC"
                );
        }
        // echo json_encode($params, JSON_PRETTY_PRINT);    

        $result = $model::getAll($params);
        $this->output($result);

        return array(
			"draw"            => !empty($this->request->get('draw')) ?
				intval( $this->request->get('draw') ) :
				0,
			"recordsTotal"    => intval( count($result) ),
			"recordsFiltered" => intval( $this->allData($params) ),
			"data"            => $this->output( $result )
		);

    }

    private function allData($filter = array()){
        $model = $this->newEntity();
        $params = array(
            'where' => isset($filter['where']) ? $filter['where'] : null,
            'group' => isset($filter['group']) ? $filter['group'] : null,
            'order' => isset($filter['order']) ? $filter['order'] : null,
        );
        return $model::countAll($params);
    }

    private function output($datas){
        $out = array();
        foreach($datas as $data){
			$row = array();
            foreach($this->column as $column){
                $rowdata = $column['callback']($data);
                $row[] = $rowdata;

                if($this->dtRowId && $this->dtRowId == $column['column']){
                    $rowid = $this->dtRowId;
                    $row['DT_RowId'] = $data->$rowid;
                }

                $row['DT_RowClass'] = $this->dtRowClass;
            }
			$out[] = $row;
        }
        return $out;


    }

    public function addColumn($column, $callback, $searchable = true, $orderable = true, $isdefaultorder = false){

        $columns = array('column' => $column, 
                        'callback' => $callback, 
                        'searchable' => $searchable, 
                        'orderable' => $orderable,
                        'isdefaultorder' => $isdefaultorder
                    );
        array_push($this->column, $columns);
        $this->columnCounter++;
        return $this;
    }

    public function addDtRowClass($className){
        $this->dtRowClass = $className;
        return $this;
    }

    public function addDtRowId($columName){
        $this->dtRowId = $columName;
        return $this;
    }

    


}