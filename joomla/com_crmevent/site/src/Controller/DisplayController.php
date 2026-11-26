<?php

namespace Alliance\Component\Crmevent\Site\Controller;

defined('_JEXEC') or die;

use Alliance\Component\Crmevent\Site\Helper\SuiteCrmClient;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class DisplayController extends BaseController
{
    protected $default_view = 'events';

    public function register(): void
    {
        Session::checkToken() or jexit('Invalid Token');

        $app = Factory::getApplication();
        $input = $app->input;

        $eventId = $input->getString('event_id', '');
        $seatIdsRaw = $input->getString('selected_seat_ids', '');
        $seatIds = array_filter(array_map('trim', explode(',', $seatIdsRaw)));

        $payload = [
            'event_id'             => $eventId,
            'ticket_type_id'       => $input->getString('ticket_type_id', ''),
            'unit_price'           => $input->getFloat('unit_price', 0.0),
            'seat_ids'             => $seatIds,
            'first_name'           => $input->getString('first_name', ''),
            'last_name'            => $input->getString('last_name', ''),
            'email'                => $input->getString('email', ''),
            'phone'                => $input->getString('phone', ''),
            'dietary_restrictions' => $input->getString('dietary_restrictions', ''),
        ];

        try {
            $client = new SuiteCrmClient();
            $confirmation = $client->createMultiSeatRegistration($payload);
            $app->setUserState('com_crmevent.confirmation', $confirmation);
            $app->enqueueMessage(
                'Seats reserved! Confirmation Code: ' . htmlspecialchars($confirmation['name'], ENT_QUOTES, 'UTF-8'),
                'success'
            );
        } catch (\Throwable $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_crmevent&view=seatmap&event_id=' . urlencode($eventId), false));
    }
}
