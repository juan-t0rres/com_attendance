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

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;

// Get query param for report id.
$uri = Uri::getInstance();
$report_id = $uri->getVar('id');

// Load report from db.
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__attendance_reports');
$query->where('id = ' . $report_id);
$db->setQuery((string) $query);
$report = $db->loadObject();
$report_date_created = new Date($report->date_created);
$report_date_created = $report_date_created->format('m/d/Y');

function sort_name($user_a, $user_b) {
    return strcmp($user_a->name, $user_b->name);
}

function get_user($id) {
    return JFactory::getUser($id);
}

// Load names and sort
$user_ids = json_decode($report->present);
$present_users = array_map(get_user, $user_ids);
usort($present_users, sort_name);

$user_ids = json_decode($report->late);
$late_minutes = json_decode($report->late_minutes);
$late_users = [];
$len = count($user_ids);
for ($i = 0; $i < $len; $i++) {
    $late_user = new stdClass();
    $late_user->id = $user_ids[$i];
    $late_user->name = get_user($late_user->id)->name;
    $late_user->minutes = $late_minutes[$i];
    array_push($late_users, $late_user);
}
usort($late_users, sort_name);

$user_ids = json_decode($report->absent);
$absent_users = array_map(get_user, $user_ids);
usort($absent_users, sort_name);

?>

<style>
    .details {
        padding: 5px;
        display:inline-block
    }
</style>

<h2>View Attendance Report</h2>
<div class="details fw-light">
    <div><b>Created By:</b> <?php echo $report->created_by; ?></div>
    <div><b>Date Created:</b> <?php echo $report_date_created; ?></div>
</div>
<table class="table">
    <tr>
        <th>Name</th>
        <th>Status</th>
    </tr>
    <?php foreach ($present_users as $present_user): ?>
        <tr class="table-success">
            <td><?= $present_user->name; ?></td>
            <td>Present</td>
        </tr>
    <?php endforeach ?>
    <?php foreach ($late_users as $late_user): ?>
        <tr class="table-warning">
            <td><?= $late_user->name; ?></td>
            <td>Late (<?= $late_user->minutes; ?> minutes)</td>
        </tr>
    <?php endforeach ?>
    <?php foreach ($absent_users as $absent_user): ?>
        <tr class="table-danger">
            <td><?= $absent_user->name; ?></td>
            <td>Absent</td>
        </tr>
    <?php endforeach ?>
</table>
