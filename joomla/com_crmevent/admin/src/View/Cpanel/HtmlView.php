<?php

namespace Alliance\Component\Crmevent\Administrator\View\Cpanel;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public string $suitecrmUrl = '';
    public string $suitecrmUser = '';

    public function display($tpl = null): void
    {
        $params = ComponentHelper::getParams('com_crmevent');
        $this->suitecrmUrl = (string) $params->get('suitecrm_url', '');
        $this->suitecrmUser = (string) $params->get('suitecrm_user', '');

        ToolbarHelper::title('CRMEvent - SuiteCRM Integration');
        ToolbarHelper::preferences('com_crmevent');

        $this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/cpanel');

        parent::display($tpl);
    }
}