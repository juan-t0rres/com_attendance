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
use Joomla\CMS\Language\Text;

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('".$output."');</script>";
}

$app = Factory::getApplication();
$date = Factory::getDate();
$date_str = $date->format(Text::_('DATE_FORMAT_FILTER_DATE'));
$input = $app->input;

$groupId = 2;
$access = new JAccess();
$members = $access->getUsersByGroup($groupId);

$rows = '';
$users = [];
foreach ($members as $id) {
    $user = JFactory::getUser($id);
    array_push($users, $user);
    $rows .= '<tr>';
    $rows .= '<td>' . $user->name . '</td>';
    $rows .= '<td><input type="checkbox" id="' . $user->id . '" name="' . $user->id . '" value="' . $user->id . '"></td>';
    $rows .= '</tr>';
}

if(array_key_exists('submit', $_POST)) {
    $present = [];
    $absent = [];
    foreach ($users as $user) {
        $is_present = $input->get($user->id, False);
        if($is_present) {
            debug_to_console('Marked present: ' . $user->name);
            array_push($present, $user->id);
        }
        else {
            array_push($absent, $user->id);
        }
    }
    $report = new stdClass();
    $report->present = json_encode($present);
    $report->absent = json_encode($absent);
    $db = JFactory::getDbo();
    $db->insertObject('#__attendance_reports', $report);
}
?>
<style>
    .attendance-table td {
        padding: 8px;
    }

    .attendance-table th {
        padding-left: 8px;
    }

    .attendance-table {
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

<h2>New Attendance Report</h2>
<h3><?php echo $date_str; ?></h3>
<form method="post"> 
    <table class="attendance-table">
        <tr>
            <th>Name</th>
            <th>Present</th>
        </tr>
        <?php echo $rows; ?>
    </table>
    <input type="submit" name="submit" value="Submit">
</form>