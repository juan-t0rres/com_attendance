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
$query->select('id,present,absent');
$query->from('#__attendance_reports');
$db->setQuery((string) $query);
$reports = $db->loadObjectList();

if ($reports)
{
    foreach ($reports as $report)
    {
        debug_to_console($report->id);
        debug_to_console($report->present);
        debug_to_console($report->absent);
    }
}

?>

<h2>Attendance</h2>
<a href="<?php echo JURI::current(); ?>?view=create">Create New Attendance Report</a>