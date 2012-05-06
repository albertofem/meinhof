<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Site\SiteInterface;

class UpdatePostsAction extends OutputAction
{
    protected $site;
    protected $templating;
    protected $output;

    public function __construct(SiteInterface $site, EngineInterface $templating, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->templating = $templating;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $posts = $this->site->getPosts();

        $globals = $this->site->getGlobals();
        $globals['posts'] = $posts;

        $this->writeOutputLine(sprintf("updating %d posts...", count($posts)), 2);

        foreach($posts as $post){
            if(!$post instanceof PostInterface){
                throw new \RuntimeException("Site returned invalid post.");
            }
            $params = $globals;
            $params['post'] = $post;

            // render post view
            $key = $post->getViewTemplatingKey();

            if(!$this->templating->exists($key)){
                throw new \InvalidArgumentException("View template '${vkey}' does not exist.");
            }
            if(!$this->templating->supports($key)){
                throw new \InvalidArgumentException("View template '${vkey}' does not have a valid format.");
            }            
            $content = $this->templating->render($key, $params);
            
            $this->site->savePost($post, $content);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}