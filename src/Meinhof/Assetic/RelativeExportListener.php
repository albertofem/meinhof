<?php

namespace Meinhof\Assetic;

use Meinhof\Exporter\ExportEvent;

/**
 * Fixes the asset factory to reflect the relative target path.
 *
 * For example if exporting a page to pages/test/test.html,
 * it will set the base url for the assets to ../../
 *
 * @see Meinhof\\Exporter\\ExportEvent
 */
class RelativeExportListener
{
    protected $factory;

    public function __construct(RelativeAssetFactory $factory)
    {
        $this->factory = $factory;
    }

    public function beforeExport(ExportEvent $event)
    {
        $base = $event->getRelativeRoot();
        $this->factory->setBaseTargetPath($base);
    }

    public function afterExport(ExportEvent $event)
    {
        $this->factory->setBaseTargetPath('');
    }
}