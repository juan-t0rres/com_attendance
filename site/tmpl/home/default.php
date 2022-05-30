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
        $reports_html .= '<div>' . $report->id . ' - <a href="' . $href . '">' . $report->date_created . '</a></div>';
        debug_to_console($report->id);
        debug_to_console($report->present);
        debug_to_console($report->absent);
        debug_to_console($report->date_created);
    }
}

?>

<style>
    .reports {
        display: flex;
        flex-direction: column;
    }

    .section {
        margin-bottom: 20px;
        padding: 10px;
    }
</style>

<div class="section">
    <h2>Attendance</h2>
    <a href="<?php echo JURI::current(); ?>?view=create">Create New Attendance Report</a>
</div>

<div class="section">
    <h3>Past Attendance Reports</h3>
    <div class="reports">
        <?php echo $reports_html; ?>
    </div>
</div>