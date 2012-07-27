<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Site\SiteInterface;
use Meinhof\Exporter\SiteExporterInterface;

/**
 * This action calls the exporter on all the categories.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateCategoriesAction extends OutputAction
{
    protected $site;
    protected $exporter;
    protected $output;

    public function __construct(SiteInterface $site,
        SiteExporterInterface $exporter, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->exporter = $exporter;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $posts = $this->site->getPosts();
        $this->writeOutputLine(sprintf("updating %d posts...", count($posts)), 2);

        foreach ($posts as $post) {
            if (!$post instanceof PostInterface) {
                throw new \RuntimeException("Site returned invalid post.");
            }
            $this->exporter->exportPost($post, $this->site);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}
