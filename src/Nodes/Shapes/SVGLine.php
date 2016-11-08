<?php

namespace JangoBrick\SVG\Nodes\Shapes;

use JangoBrick\SVG\Nodes\SVGNode;
use JangoBrick\SVG\Rasterization\SVGRasterizer;

/**
 * Represents the SVG tag 'line'.
 * Has the special attributes x1, y1, x2, y2.
 */
class SVGLine extends SVGNode
{
    const TAG_NAME = 'line';

    /**
     * @param string|null $x1 The first point's x coordinate.
     * @param string|null $y1 The first point's y coordinate.
     * @param string|null $x2 The second point's x coordinate.
     * @param string|null $y2 The second point's y coordinate.
     */
    public function __construct($x1 = null, $y1 = null, $x2 = null, $y2 = null)
    {
        parent::__construct();

        $this->setAttributeOptional('x1', $x1);
        $this->setAttributeOptional('y1', $y1);
        $this->setAttributeOptional('x2', $x2);
        $this->setAttributeOptional('y2', $y2);
    }



    /**
     * @return string The first point's x coordinate.
     */
    public function getX1()
    {
        return $this->getAttribute('x1');
    }

    /**
     * Sets the first point's x coordinate.
     *
     * @param string $x1 The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setX1($x1)
    {
        return $this->setAttribute('x1', $x1);
    }

    /**
     * @return string The first point's y coordinate.
     */
    public function getY1()
    {
        return $this->getAttribute('y1');
    }

    /**
     * Sets the first point's y coordinate.
     *
     * @param string $y1 The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setY1($y1)
    {
        return $this->setAttribute('y1', $y1);
    }



    /**
     * @return string The second point's x coordinate.
     */
    public function getX2()
    {
        return $this->getAttribute('x2');
    }

    /**
     * Sets the second point's x coordinate.
     *
     * @param string $x2 The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setX2($x2)
    {
        return $this->setAttribute('x2', $x2);
    }

    /**
     * @return string The second point's y coordinate.
     */
    public function getY2()
    {
        return $this->getAttribute('y2');
    }

    /**
     * Sets the second point's y coordinate.
     *
     * @param string $y2 The new coordinate.
     *
     * @return $this This node instance, for call chaining.
     */
    public function setY2($y2)
    {
        return $this->setAttribute('y2', $y2);
    }



    public function rasterize(SVGRasterizer $rasterizer)
    {
        $rasterizer->render('line', array(
            'x1'    => $this->getX1(),
            'y1'    => $this->getY1(),
            'x2'    => $this->getX2(),
            'y2'    => $this->getY2(),
        ), $this);
    }
}
