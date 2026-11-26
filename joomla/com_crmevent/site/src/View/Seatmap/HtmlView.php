<?php

namespace Alliance\Component\Crmevent\Site\View\Seatmap;

defined('_JEXEC') or die;

use Alliance\Component\Crmevent\Site\Helper\SuiteCrmClient;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    public ?array $mapData = null;
    public ?array $confirmation = null;

    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        $eventId = $app->input->getString('event_id', '');
        $this->confirmation = $app->getUserState('com_crmevent.confirmation');
        $app->setUserState('com_crmevent.confirmation', null);

        if (!empty($eventId)) {
            try {
                $client = new SuiteCrmClient();
                $this->mapData = $client->getEventSeatMapData($eventId);
            } catch (\Throwable $e) {
                $app->enqueueMessage($e->getMessage(), 'error');
            }
        }

        parent::display($tpl);
    }
}
