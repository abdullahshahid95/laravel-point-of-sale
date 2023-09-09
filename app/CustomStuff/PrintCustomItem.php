<?php
namespace App\CustomStuff;

class PrintCustomItem
{
    private $name;
    private $qty;
    private $price;
    private $totalPrice;
    private $dollarSign;

    public function __construct($name = '', $qty = '', $price = '', $totalPrice = '' , $dollarSign = false)
    {
        $this -> name = $name;
        $this -> qty = $qty;
        $this -> price = $price;
        $this -> totalPrice = $totalPrice;
        $this -> dollarSign = $dollarSign;
    }

    public function __toString()
    {
        $rightCols = 11;
        $middle1Cols = 2;
        $middle2Cols = 16;
        $leftCols = 16;
        if ($this -> dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this -> name, $leftCols);

        $sign = ($this -> dollarSign ? '$ ' : '');
        $middle1 = str_pad($sign . $this -> qty, $middle1Cols, ' ', STR_PAD_LEFT);
        $middle2 = str_pad($sign . $this -> price, $middle2Cols, ' ', STR_PAD_LEFT);
        $right = str_pad($sign . $this -> totalPrice, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$middle1$middle2$right\n";
    }
}
?>