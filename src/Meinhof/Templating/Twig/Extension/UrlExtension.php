<?php

namespace Meinhof\Templating\Twig\Extension;

use Meinhof\Helper\UrlHelperInterface;

use Meinhof\Export\ExportEvent;

class UrlExtension extends \Twig_Extension
{
    protected $helper;
    protected $webroot;
    protected $parameters;

    public function __construct(UrlHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function onExport(ExportEvent $event)
    {
        $this->webroot = $event->getRelativeRoot();
        $this->parameters = $event->getParameters();
    }

    public function getName()
    {
        return 'url';
    }

    public function getFunctions()
    {
        return array(
            'url'   => new \Twig_Function_Method($this, "getUrl")
        );
    }

    public function getUrl($obj, array $params=array(), $relative=true)
    {
        if (!$obj) {
            return "";
        }
        if (is_array($this->parameters)) {
            $params = array_merge($this->parameters, $params);
        }
        if (is_object($obj)) {
            $obj = $this->helper->getUrl($obj, $params);
        }
        if ($relative && is_string($obj)) {
            return $this->webroot.$obj;
        }
        throw new \InvalidArgumentException("Could not get the url.");
    }

}
