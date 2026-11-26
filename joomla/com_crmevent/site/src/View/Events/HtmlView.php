<?php

namespace Alliance\Component\Crmevent\Site\View\Events;

defined('_JEXEC') or die;

use Alliance\Component\Crmevent\Site\Helper\SuiteCrmClient;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    public array $events = [];

    public function display($tpl = null): void
    {
        try {
            $client = new SuiteCrmClient();
            $this->events = $client->getPublicEvents();
        } catch (\Throwable $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        parent::display($tpl);
    }
}
