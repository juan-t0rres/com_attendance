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
$query->select('id,present,absent,date_created');
$query->from('#__attendance_reports');
$query->where('id = ' . $report_id);
$db->setQuery((string) $query);
$report = $db->loadObject();

$present_users = '';
$user_ids = json_decode($report->present);
foreach ($user_ids as $id) {
    $user = JFactory::getUser($id);
    $present_users .= '<div>' . $user->name . '</div>';
}

$absent_users = '';
$user_ids = json_decode($report->absent);
foreach ($user_ids as $id) {
    $user = JFactory::getUser($id);
    $absent_users .= '<div>' . $user->name . '</div>';
}
?>

<style>
    .section {
        margin-top: 10px;
        padding: 10px;
    }
</style>

<h2>View Report</h2>
<div class="section">
    <h4>Present</h4>
    <?php echo $present_users; ?>
</div>
<div class="section">
    <h4>Absent</h4>
    <?php echo $absent_users; ?>
</div>


