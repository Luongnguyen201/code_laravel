<?php 
namespace app\Components;
use App\Models\Contact;
    class Recusive{

        private $data;
        private $htmlseclect='';
        private $html;
    public function __construct($data)
        {   
            $this->html='';
            $this->data = $data;
        }
    public function cateCategoryRecusive($perent_id,$id=0, $text=''){
        foreach($this->data as $value){
            if($id==$value['parent_id']){
                if(!empty($perent_id) && $perent_id==$value['id']){
                    $this->htmlseclect .="<option selected value='".$value['id']."'>".$text.$value['name']."</option>";
                }else{
                    $this->htmlseclect .="<option value='".$value['id']."'>".$text.$value['name']."</option>";
                }
                $this->cateCategoryRecusive($perent_id,$value['id'],$text.'----');
            }
        }
        return $this->htmlseclect;
    }
    public function ManuRecusiveAdd($parent_id=0, $submark=''){
        $this->data->where('parent_id',$parent_id)->get();
        foreach($this->data as $value){
            $this->html .= "<option value='".$value->id."'>".$submark.$value->name."</option>";
            $this->ManuRecusiveAdd($value->id,$submark='--');
        }
        return $this->html;
    }
}
?>