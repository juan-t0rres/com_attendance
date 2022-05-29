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

$groupId = 2;
$access = new JAccess();
$members = $access->getUsersByGroup($groupId);

$rows = '';
foreach ($members as $id) {
    $user = JFactory::getUser($id);
    $rows .= '<tr>';
    $rows .= '<td>' . $user->name . '</td>';
    $rows .= '<td>' . $user->username . '</td>';
    $rows .= '<td>' . $user->email . '</td>';
    $rows .= '<td>' . $user->id . '</td>';
    $rows .= '</tr>';
}

?>
<style>
    .attendance-table th,
    td {
        padding: 15px;
    }
</style>

<h2>New Attendance Report</h2>
<table class="attendance-table">
    <tr>
        <th>Name</th>
        <th>Username</th>
        <th>E-mail</th>
        <th>Unique ID</th>
    </tr>
    <?php echo $rows; ?>
</table>
<a href="/joomla4/index.php/component/attendance/?view=test">Test Link to New View</a>