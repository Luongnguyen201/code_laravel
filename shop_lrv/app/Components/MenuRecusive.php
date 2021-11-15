<?php
namespace app\Components;
use app\Models\Manus;
    class MenuRecusive{
        private $html;
        public function __construct()
        {
            $this->html='';
        }
        public function ManuRecusiveAdd($parent_id=0, $submark=''){
            $data = Manus::where('parent_id',$parent_id)->get();
            foreach($data as $value){
                $this->html .= "<option value='".$value->id."'>".$submark.$value->name."</option>"; 
                $this->ManuRecusiveAdd($value->id,$submark.'--');  
            }
            return $this->html;
        }
        public function ManuRecusiveSelect($id,$parent_id=0, $submark=''){
            $data = Manus::where('parent_id',$parent_id)->get();
            foreach($data as $value){
                if(!empty($id) && $id == $value->id){
                    $this->html .= "<option selected value='".$value->id."'>".$submark.$value->name."</option>"; 
                }else{
                    $this->html .= "<option value='".$value->id."'>".$submark.$value->name."</option>"; 
                }
                $this->ManuRecusiveSelect($id,$value->id,$submark.'--');  
            }
            return $this->html;
        }
    }
?>