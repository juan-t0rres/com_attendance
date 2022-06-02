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

// get query param for report id
$uri = Uri::getInstance();
$report_id = $uri->getVar('id');

// load report from db
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('id,present,absent,date_created,created_by');
$query->from('#__attendance_reports');
$query->where('id = ' . $report_id);
$db->setQuery((string) $query);
$report = $db->loadObject();

$present_users = '';
$user_ids = json_decode($report->present);
$users = [];
foreach ($user_ids as $id) {
    $user = JFactory::getUser($id);
    array_push($users, $user);
}
usort($users, function ($user_a, $user_b) {
    return strcmp($user_a->name, $user_b->name);
});
foreach ($users as $user) {
    $present_users .= '<tr class="table-success">';
    $present_users .= '<td>' . $user->name . '</td>';
    $present_users .= '<td>Yes</td>';
    $present_users .= '</tr>';
}

$absent_users = '';
$user_ids = json_decode($report->absent);
$users = [];
foreach ($user_ids as $id) {
    $user = JFactory::getUser($id);
    array_push($users, $user);
}
usort($users, function ($user_a, $user_b) {
    return strcmp($user_a->name, $user_b->name);
});
foreach ($users as $user) {
    $absent_users .= '<tr class="table-danger">';
    $absent_users .= '<td>' . $user->name . '</td>';
    $absent_users .= '<td>No</td>';
    $absent_users .= '</tr>';
}
?>

<style>
    .details {
        background-color: #eaeaea;
        padding: 8px;
        display:inline-block
    }
</style>

<h2>View Report</h2>
<div class="details fw-light">
    <div><b>Created By:</b> <?php echo $report->created_by; ?></div>
    <div><b>Date Created:</b> <?php echo $report->date_created; ?></div>
</div>
<table class="table">
    <tr>
        <th>Name</th>
        <th>Present</th>
    </tr>
    <?php echo $present_users; ?>
    <?php echo $absent_users; ?>
</table>
