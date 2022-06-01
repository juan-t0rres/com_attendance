<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_attendance
 *
 * @copyright   Copyright (C) 2020 John Smith. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Factory;

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
    echo "<script>console.log('".$output."');</script>";
}

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('id,present,absent,date_created');
$query->from('#__attendance_reports');
$db->setQuery((string) $query);
$reports = $db->loadObjectList();

$reports_html = 'No past reports.';
if ($reports)
{
    $reports_html = '';
    foreach ($reports as $report)
    {
        $href = JURI::current() . '?view=report&id=' . $report->id;
        $reports_html .= '<a class="list-group-item list-group-item-action" href="' . $href . '">' .   $report->id . '. ' .  $report->date_created . '</a>';
        debug_to_console($report->id);
        debug_to_console($report->present);
        debug_to_console($report->absent);
        debug_to_console($report->date_created);
    }
}

?>

<div class="mb-5">
    <h2>Attendance</h2>
    <a class="btn btn-primary btn-sm" href="<?php echo JURI::current(); ?>?view=create">Create New Attendance Report</a>
</div>

<div>
    <h3>Past Attendance Reports</h3>
    <div class="list-group">
        <?php echo $reports_html; ?>
    </div>
</div>