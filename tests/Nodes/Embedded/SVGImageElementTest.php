<?php

namespace SVG;

use SVG\Nodes\Embedded\SVGImageElement;

/**
 * @SuppressWarnings(PHPMD)
 */
 class SVGImageElementTest extends \PHPUnit\Framework\TestCase
{
    public function test__construct()
    {
        // should not set any attributes by default
        $obj = new SVGImageElement();
        $this->assertSame(array(), $obj->getSerializableAttributes());

        // should set attributes when provided
        $obj = new SVGImageElement('test-href', 10, 10, 100, 100);
        $this->assertSame(array(
            'xlink:href' => 'test-href',
            'x' => '10',
            'y' => '10',
            'width' => '100',
            'height' => '100',
        ), $obj->getSerializableAttributes());
    }

    public function testGetHref()
    {
        // should return xlink:href when available
        $obj = new SVGImageElement();
        $obj->setAttribute('xlink:href', 'test-xlink-href');
        $obj->setAttribute('href', 'test-href');
        $this->assertSame('test-xlink-href', $obj->getHref());

        // should return href when xlink:href not available
        $obj = new SVGImageElement();
        $obj->setAttribute('href', 'test-href');
        $this->assertSame('test-href', $obj->getHref());

        // should return null when no href available
        $obj = new SVGImageElement();
        $this->assertNull($obj->getHref());
    }

    public function testSetHref()
    {
        $obj = new SVGImageElement();

        // should set xlink:href
        $obj->setHref('test-href');
        $this->assertSame('test-href', $obj->getAttribute('xlink:href'));
    }

    public function testGetX()
    {
        $obj = new SVGImageElement();

        // should return the attribute
        $obj->setAttribute('x', 42);
        $this->assertSame('42', $obj->getX());
    }

    public function testSetX()
    {
        $obj = new SVGImageElement();

        // should update the attribute
        $obj->setX(42);
        $this->assertSame('42', $obj->getAttribute('x'));
    }

    public function testGetY()
    {
        $obj = new SVGImageElement();

        // should return the attribute
        $obj->setAttribute('y', 42);
        $this->assertSame('42', $obj->getY());
    }

    public function testSetY()
    {
        $obj = new SVGImageElement();

        // should update the attribute
        $obj->setY(42);
        $this->assertSame('42', $obj->getAttribute('y'));
    }

    public function testGetWidth()
    {
        $obj = new SVGImageElement();

        // should return the attribute
        $obj->setAttribute('width', 42);
        $this->assertSame('42', $obj->getWidth());
    }

    public function testSetWidth()
    {
        $obj = new SVGImageElement();

        // should update the attribute
        $obj->setWidth(42);
        $this->assertSame('42', $obj->getAttribute('width'));
    }

    public function testGetHeight()
    {
        $obj = new SVGImageElement();

        // should return the attribute
        $obj->setAttribute('height', 42);
        $this->assertSame('42', $obj->getHeight());
    }

    public function testSetHeight()
    {
        $obj = new SVGImageElement();

        // should update the attribute
        $obj->setHeight(42);
        $this->assertSame('42', $obj->getAttribute('height'));
    }

    public function testRasterize()
    {
        $obj = new SVGImageElement('test-href', 10, 10, 100, 100);

        $rast = $this->getMockBuilder('\SVG\Rasterization\SVGRasterizer')
            ->disableOriginalConstructor()
            ->getMock();

        // should call image renderer with correct options
        $rast->expects($this->once())->method('render')->with(
            $this->identicalTo('image'),
            $this->identicalTo(array(
                'href' => 'test-href',
                'x' => '10',
                'y' => '10',
                'width' => '100',
                'height' => '100',
            )),
            $this->identicalTo($obj)
        );
        $obj->rasterize($rast);

        // should not rasterize with 'display: none' style
        $obj->setStyle('display', 'none');
        $obj->rasterize($rast);

        // should not rasterize with 'visibility: hidden' or 'collapse' style
        $obj->setStyle('display', null);
        $obj->setStyle('visibility', 'hidden');
        $obj->rasterize($rast);
        $obj->setStyle('visibility', 'collapse');
        $obj->rasterize($rast);
    }
}
