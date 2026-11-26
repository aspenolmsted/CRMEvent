<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

$optionsUrl = Route::_('index.php?option=com_config&view=component&component=com_crmevent');
?>
<div class="card">
    <div class="card-body">
        <h3>CRMEvent SuiteCRM Integration</h3>
        <p>Configure your SuiteCRM 7.14 REST v4_1 connection using the <strong>Options</strong> button in the top-right toolbar (or the button below).</p>

        <table class="table table-bordered" style="max-width: 650px;">
            <tbody>
                <tr>
                    <th style="width: 220px;">SuiteCRM Base URL</th>
                    <td><?php echo $this->suitecrmUrl !== '' ? htmlspecialchars($this->suitecrmUrl, ENT_QUOTES, 'UTF-8') : '<span class="badge bg-warning text-dark">Not Configured</span>'; ?></td>
                </tr>
                <tr>
                    <th>API Username</th>
                    <td><?php echo $this->suitecrmUser !== '' ? htmlspecialchars($this->suitecrmUser, ENT_QUOTES, 'UTF-8') : '<span class="badge bg-warning text-dark">Not Configured</span>'; ?></td>
                </tr>
                <tr>
                    <th>Frontend Events URL</th>
                    <td><code>index.php?option=com_crmevent&amp;view=events</code></td>
                </tr>
            </tbody>
        </table>

        <a href="<?php echo $optionsUrl; ?>" class="btn btn-primary">Configure SuiteCRM API Settings</a>
    </div>
</div>