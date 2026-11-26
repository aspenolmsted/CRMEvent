<?php

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
?>
<div class="com-crmevent-events">
    <h2>Upcoming Events</h2>

    <?php if (empty($this->events)) : ?>
        <div class="alert alert-info">No public events are currently open for registration.</div>
    <?php else : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($this->events as $ev) : ?>
                <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 18px; background: #ffffff;">
                    <h3 style="margin-top: 0;"><?php echo htmlspecialchars($ev['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p style="color: #475569; font-size: 14px; margin: 6px 0;">
                        <strong>Starts:</strong> <?php echo htmlspecialchars($ev['date_start'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p style="color: #475569; font-size: 14px; margin: 6px 0;">
                        <strong>Seats Available:</strong>
                        <?php echo (int) ($ev['seats_remaining_c'] ?? 0); ?> / <?php echo (int) ($ev['capacity_c'] ?? 0); ?>
                    </p>
                    <?php if (!empty($ev['description'])) : ?>
                        <p style="font-size: 14px; color: #334155;">
                            <?php echo nl2br(htmlspecialchars($ev['description'], ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    <?php endif; ?>
                    <a class="btn btn-primary"
                       href="<?php echo Route::_('index.php?option=com_crmevent&view=seatmap&event_id=' . urlencode($ev['id'])); ?>">
                        Select Seats &amp; Register
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
