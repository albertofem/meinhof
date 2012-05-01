<?php

namespace Meinhof\Tests\Assetic;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

use Meinhof\Assetic\ResourceLoaderInterface;
use Meinhof\Assetic\DelegatingResourceLoader;

class DelegatingResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;
    protected $manager;

    public function setUp()
    {
        $parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');
        $template = $this->getMock('Symfony\\Component\\Templating\\TemplateReferenceInterface');

        $template->expects($this->any())
            ->method('get')
            ->with($this->equalTo('engine'))
            ->will($this->returnValue('twig'));

        $parser->expects($this->any())
            ->method('parse')
            ->with($this->equalTo('test.twig'))
            ->will($this->returnValue($template));

        $this->loader = new DelegatingResourceLoader($parser);

        $this->manager = $this->getMockBuilder('Assetic\\Factory\\LazyAssetManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testImplementation()
    {
        $this->assertTrue($this->loader instanceof ResourceLoaderInterface);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadingWithBadParser()
    {
        $parser = $this->getMock('Symfony\\Component\\Templating\\TemplateNameParserInterface');

        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnValue(null));

        $loader = new DelegatingResourceLoader($parser);
        $loader->load('test', $this->manager);
    }    

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadingWithoutLoader()
    {
        $this->loader->load('test.twig', $this->manager);
    }

    public function testLoadingWithLoader()
    {
        $loader = $this->getMock('Meinhof\\Assetic\\ResourceLoaderInterface');
        $loader->expects($this->once())
            ->method('load')
            ->with($this->equalTo('test.twig'), $this->equalTo($this->manager))
            ->will($this->returnValue('result'));

        $this->loader->setLoader('twig', $loader);
        $this->assertEquals('result', $this->loader->load('test.twig', $this->manager));
    } 
}