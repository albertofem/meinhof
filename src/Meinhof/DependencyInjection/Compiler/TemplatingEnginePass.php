<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TemplatingEnginePass implements CompilerPassInterface
{
    protected $engine_types = array('post', 'content', 'view');

    public function process(ContainerBuilder $container)
    {
        $method = 'addEngine';
        $prefix = 'templating.';
        $tag = 'templating.engine';

        $defs = array();
        foreach ($this->engine_types as $type) {
            $key = $prefix.$type;
            if ($container->hasDefinition($key)) {
                $defs[$type] = $container->getDefinition($key);
            }
        }

        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                $type = $this->getTypeFromAttributes($attributes);
                $args = array(new Reference($id));
                if (in_array($type, array_keys($defs))) {
                    $defs[$type]->addMethodCall($method, $args);
                } elseif ($type === 'all') {
                    foreach ($defs as $def) {
                        $def->addMethodCall($method, $args);
                    }
                } else {
                    throw new \InvalidArgumentException("Invalid templating engine type '${type}'.");
                }
            }
        }
    }

    protected function getTypeFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['type'])) {
            return 'all';
        }

        return $attrs['type'];
    }
}
