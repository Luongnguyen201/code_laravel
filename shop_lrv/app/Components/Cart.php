<?php
    namespace App\Components;
    
    class Cart{
        public $Products = null;
        public $TotalPrice = 0;
        public $TotalQuantity = 0;
        public  $count = 0;

        public function __construct($cart)
        {
            if($cart){
                $this->Products = $cart->Products;
                $this->TotalPrice = $cart->TotalPrice;
                $this->TotalQuantity = $cart->TotalQuantity;
                $this->TotalQuantity = $cart->TotalQuantity;
                $this->count = $cart->count;
            }
        }

        public function Add_cart($Product, $id, $quantity, $color_product){
            $NewProduct = ['quantity' => $quantity, 'price' => $Product->price*(100-$Product->promotion)/100 * $quantity, 'promotion' => $Product->promotion, 'color' => $color_product ,'productInfo' => $Product];
            if(!empty($this->Products)){
                if(array_key_exists($id,$this->Products)){
                    $NewProduct = $this->Products[$id];
                    $NewProduct['quantity'] += $quantity;
                    $NewProduct['price'] +=  $Product->price*(100-$Product->promotion)/100 * $quantity;
                    $NewProduct['promotion'] = $Product->promotion;
                }else{
                    $this->count += 1;
                }
            }else{
                $this->count += 1;
            }
            $this->Products[$id] =  $NewProduct;
            $this->TotalPrice +=  $Product->price*(100-$Product->promotion)/100 * $quantity;
            $this->TotalQuantity +=  $quantity;
        }

        public function increaseQuantity($id,$quantity,$Product){
            $NewProduct = ['quantity' => $quantity, 'price' => $Product->price*(100-$Product->promotion)/100 * $quantity,'promotion' => $Product->promotion , 'productInfo' => $Product];
            if(!empty($this->Products)){
                if(array_key_exists($id,$this->Products)){
                    $NewProduct = $this->Products[$id];
                    $NewProduct['quantity'] += $quantity;
                    $NewProduct['price'] +=  $Product->price*(100-$Product->promotion)/100 * $quantity;
                    $NewProduct['promotion'] = $Product->promotion;
                }
            }
            $this->Products[$id] =  $NewProduct;
            $this->TotalPrice +=  $Product->price*(100-$Product->promotion)/100 * $quantity;
            $this->TotalQuantity +=  $quantity;
        }

        public function decreaseQuantity($id,$quantity,$Product){
            $NewProduct = ['quantity' => $quantity, 'price' => $Product->price*(100-$Product->promotion)/100 * $quantity,'promotion' => $Product->promotion , 'productInfo' => $Product];
            if(!empty($this->Products)){
                if(array_key_exists($id,$this->Products)){
                    $NewProduct = $this->Products[$id];
                    $NewProduct['quantity'] -= $quantity;    
                    $NewProduct['promotion'] = $Product->promotion;
                    if($NewProduct['quantity'] >= 1 ){
                        $NewProduct['price'] -=  $Product->price*(100-$Product->promotion)/100 * $quantity;
                        $this->Products[$id] =  $NewProduct;
                        $this->TotalPrice -= $Product->price*(100-$Product->promotion)/100 * $quantity;
                        $this->TotalQuantity -=  $quantity;
                    }
                }
            }
        }

        public function DeleteItemCart($id){
            $this->TotalQuantity -= $this->Products[$id]['quantity'];
            $this->TotalPrice -= $this->Products[$id]['price'];
            $this->count -= 1;
            unset($this->Products[$id]);
        }


    }
?>