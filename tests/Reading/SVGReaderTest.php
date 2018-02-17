<?php

namespace SVG;

use SVG\Reading\SVGReader;
use SVG\Utilities\SVGStyleParser;

/**
 * @SuppressWarnings(PHPMD)
 */
class SVGReaderTest extends \PHPUnit\Framework\TestCase
{
    // THE TESTS IN THIS CLASS DO NOT ADHERE TO THE STANDARD LAYOUT
    // OF TESTING ONE CLASS METHOD PER TEST METHOD
    // BECAUSE THE CLASS UNDER TEST IS A SINGLE-FEATURE CLASS

    private $xml, $xmlNoViewBox, $xmlNoWH, $xmlUnknown, $xmlEntities;

    public function setUp()
    {
        $this->xml  = '<?xml version="1.0" encoding="utf-8"?>';
        $this->xml .= '<svg width="37" height="42" viewBox="10 20 74 84" '.
            'xmlns="http://www.w3.org/2000/svg" '.
            'xmlns:xlink="http://www.w3.org/1999/xlink" '.
            'xmlns:testns="test-namespace">';
        $this->xml .= '<rect id="testrect" testns:attr="test" xlink:foo="bar" '.
            'fill="#ABCDEF" style="opacity: .5; stroke: #AABBCC;" />';
        $this->xml .= '<g>';
        $this->xml .= '<circle cx="10" cy="20" r="42" />';
        $this->xml .= '<ellipse cx="50" cy="60" rx="10" ry="20" />';
        $this->xml .= '</g>';
        $this->xml .= '</svg>';

        $this->xmlNoViewBox  = '<?xml version="1.0" encoding="utf-8"?>';
        $this->xmlNoViewBox .= '<svg width="37" height="42" '.
            'xmlns="http://www.w3.org/2000/svg" '.
            'xmlns:xlink="http://www.w3.org/1999/xlink">';
        $this->xmlNoViewBox .= '</svg>';

        $this->xmlNoWH  = '<?xml version="1.0" encoding="utf-8"?>';
        $this->xmlNoWH .= '<svg viewBox="10 20 74 84" '.
            'xmlns="http://www.w3.org/2000/svg">';
        $this->xmlNoWH .= '</svg>';

        $this->xmlUnknown  = '<?xml version="1.0" encoding="utf-8"?>';
        $this->xmlUnknown .= '<svg xmlns="http://www.w3.org/2000/svg">';
        $this->xmlUnknown .= '<circle cx="10" cy="20" r="42" />';
        $this->xmlUnknown .= '<unknown foo="bar"><baz /></unknown>';
        $this->xmlUnknown .= '<ellipse cx="50" cy="60" rx="10" ry="20" />';
        $this->xmlUnknown .= '</svg>';

        $this->xmlEntities  = '<?xml version="1.0" encoding="utf-8"?>';
        $this->xmlEntities .= '<svg xmlns="http://www.w3.org/2000/svg">';
        $this->xmlEntities .= '<style id="&quot; foo&amp;bar&gt;" '.
            'style="display: &amp;none">&quot; foo&amp;bar&gt;</style>';
        $this->xmlEntities .= '</svg>';
    }

    public function testShouldReturnAnImageOrNull()
    {
        // should return an instance of SVGImage
        $result = (new SVGReader())->parseString($this->xml);
        $this->assertInstanceOf('\SVG\SVGImage', $result);

        // should return null when parsing fails
        $result = (new SVGReader())->parseString('<rect />');
        $this->assertNull($result);
    }

    public function testShouldSetAllAttributesAndNamespaces()
    {
        // should retain all document attributes and namespaces
        $result = (new SVGReader())->parseString($this->xml);
        $this->assertEquals(array(
            'xmlns' => 'http://www.w3.org/2000/svg',
            'xmlns:xlink' => 'http://www.w3.org/1999/xlink',
            'xmlns:testns' => 'test-namespace',
            'width' => '37',
            'height' => '42',
            'viewBox' => '10 20 74 84',
        ), $result->getDocument()->getSerializableAttributes());

        // should deal with missing viewBox
        $result = (new SVGReader())->parseString($this->xmlNoViewBox);
        $this->assertEquals(array(
            'xmlns' => 'http://www.w3.org/2000/svg',
            'xmlns:xlink' => 'http://www.w3.org/1999/xlink',
            'width' => '37',
            'height' => '42',
        ), $result->getDocument()->getSerializableAttributes());

        // should deal with missing width/height
        $result = (new SVGReader())->parseString($this->xmlNoWH);
        $this->assertEquals(array(
            'xmlns' => 'http://www.w3.org/2000/svg',
            'xmlns:xlink' => 'http://www.w3.org/1999/xlink',
            'viewBox' => '10 20 74 84',
        ), $result->getDocument()->getSerializableAttributes());

        // should set all attributes, including namespace prefixed ones
        $result = (new SVGReader())->parseString($this->xml);
        $rect = $result->getDocument()->getChild(0);
        $this->assertEquals(array(
            'id' => 'testrect',
            'testns:attr' => 'test',
            'xlink:foo' => 'bar',
        ), $rect->getSerializableAttributes());
    }

    public function testShouldSetStyles()
    {
        $result = (new SVGReader())->parseString($this->xml);
        $rect = $result->getDocument()->getChild(0);

        // should detect style attributes
        $this->assertNull($rect->getAttribute('fill'));
        $this->assertSame('#ABCDEF', $rect->getStyle('fill'));

        // should parse and set the 'style' attribute
        $this->assertEquals('.5', $rect->getStyle('opacity'));
        $this->assertEquals('#AABBCC', $rect->getStyle('stroke'));
    }

    public function testShouldRecursivelyAddChildren()
    {
        // should recursively add all child nodes

        $result = (new SVGReader())->parseString($this->xml);
        $g = $result->getDocument()->getChild(1);

        $this->assertSame(2, $g->countChildren());

        $circle = $g->getChild(0);
        $this->assertEquals(array(
            'cx' => '10',
            'cy' => '20',
            'r' => '42',
        ), $circle->getSerializableAttributes());

        $ellipse = $g->getChild(1);
        $this->assertEquals(array(
            'cx' => '50',
            'cy' => '60',
            'rx' => '10',
            'ry' => '20',
        ), $ellipse->getSerializableAttributes());
    }

    public function testShouldIgnoreUnknownNodes()
    {
        // should skip unknown node types without failing
        $result = (new SVGReader())->parseString($this->xmlUnknown);
        $doc = $result->getDocument();
        $this->assertSame(2, $doc->countChildren());
        $this->assertSame('circle', $doc->getChild(0)->getName());
        $this->assertSame('ellipse', $doc->getChild(1)->getName());
    }

    public function testShouldDecodeEntities()
    {
        $result = (new SVGReader())->parseString($this->xmlEntities);
        $doc = $result->getDocument();

        // should decode entities in attributes
        $this->assertSame('" foo&bar>', $doc->getChild(0)->getAttribute('id'));
        $this->assertSame('&none', $doc->getChild(0)->getStyle('display'));

        // should decode entities in style body
        $this->assertSame('" foo&bar>', $doc->getChild(0)->getCss());
    }

    public function testParseStylesWithEmptyString()
    {
        $this->assertCount(0, SVGStyleParser::parseStyles(''));
    }
}
