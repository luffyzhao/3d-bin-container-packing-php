<?php

namespace BinContainerPacking;

class Container extends Box
{
    protected $stackWeight = 0;
    protected $stackHeight = 0;
    protected $levels = [];

    public function __construct(string $name, int $width, int $depth, int $height)
    {
        parent::__construct($name, $width, $depth, $height);
    }

    public function rotations(): array
    {

    }

    /**
     * @return array
     */
    public function rotationsStream(): array
    {
        $result = [];
        $box = $this->clone();
        $square0 = $box->isSquare2D();
        $result[] = $box;
        // 3D 要参数调转6次
        if(!$box->isSquare3D()){
            $box = $box->clone()->rotate3D();
            $square1 = $box->isSquare2D();
            $result[] = $box;

            $box = $box->clone()->rotate3D();
            $square2 = $box->isSquare2D();
            $result[] = $box;

            if(!$square0 && !$square1 && !$square2){
                $box = $box->clone()->rotate2D3D();
                $result[] = $box;
                $box = $box->clone()->rotate3D();
                $result[] = $box;
                $box = $box->clone()->rotate3D();
                $result[] = $box;
            }
        }
        return $result;
    }


}