<?php

namespace JangoBrick\SVG\Nodes\Shapes;

use JangoBrick\SVG\Nodes\SVGNode;
use JangoBrick\SVG\Rasterization\SVGRasterizer;

class SVGLine extends SVGNode
{
    private $x1, $y1, $x2, $y2;

    public function __construct($x1, $y1, $x2, $y2)
    {
        parent::__construct();

        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2 = $x2;
        $this->y2 = $y2;
    }

    public function getX1()
    {
        return $this->x1;
    }

    public function setX1($x1)
    {
        $this->x1 = $x1;
        return $this;
    }

    public function getY1()
    {
        return $this->y1;
    }

    public function setY1($y1)
    {
        $this->y1 = $y1;
        return $this;
    }

    public function getX2()
    {
        return $this->x2;
    }

    public function setX2($x2)
    {
        $this->x2 = $x2;
        return $this;
    }

    public function getY2()
    {
        return $this->y2;
    }

    public function setY2($y2)
    {
        $this->y2 = $y2;
        return $this;
    }

    public function toXMLString()
    {
        $s  = '<line';

        $s .= ' x1="'.$this->x1.'"';
        $s .= ' y1="'.$this->y1.'"';
        $s .= ' x2="'.$this->x2.'"';
        $s .= ' y2="'.$this->y2.'"';

        $this->addStylesToXMLString($s);
        $this->addAttributesToXMLString($s);

        $s .= ' />';

        return $s;
    }

    public function rasterize(SVGRasterizer $rasterizer)
    {
        $rasterizer->render('line', array(
            'x1'    => $this->x1,
            'y1'    => $this->y1,
            'x2'    => $this->x2,
            'y2'    => $this->y2,
        ), $this);
    }
}
