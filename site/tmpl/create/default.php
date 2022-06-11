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
use Joomla\CMS\Uri\Uri;

$uri = Uri::getInstance();
$report_id = $uri->getVar('id');
$report_created_by = NULL;

$app = Factory::getApplication();
$date = Factory::getDate();
$date_str = $date->format('m/d/Y');
$input = $app->input;

// Students are group id 11
$group_id = 11;
$access = new JAccess();

$members = $access->getUsersByGroup($group_id);

$present_ids = [];
$absent_ids = [];
$late_ids = [];
if (isset($report_id)) {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__attendance_reports');
    $query->where('id = ' . $report_id);
    $db->setQuery((string) $query);
    $report = $db->loadObject();
    $report_created_by = $report->created_by;
    $date_str = $report->date_created;
    $present_ids = json_decode($report->present);
    $late_ids = json_decode($report->late);
    $absent_ids = json_decode($report->absent);
    $members = array_merge($present_ids, $absent_ids, $late_ids);
    asort($members);
}

$users = [];
foreach ($members as $id) {
    $user = JFactory::getUser($id);
    array_push($users, $user);
}
usort($users, function ($user_a, $user_b) {
    return strcmp($user_a->name, $user_b->name);
});
$rows = '';
foreach ($users as $user) {
    $is_present = in_array($user->id, $present_ids);
    $is_late = in_array($user->id, $late_ids);
    $rows .= '<tr>';
    $rows .= '<td>' . $user->name . '</td>';
    $rows .= '<td><input style="margin-left: 20px;" type="radio"'; 
    $rows .= 'name="' . $user->id . '" ';
    $rows .= 'value="present"' . ($is_present ? 'checked' : '') . '>';
    $rows .= '</td>';
    $rows .= '<td><input style="margin-left: 10px;" type="radio"'; 
    $rows .= 'name="' . $user->id . '" ';
    $rows .= 'value="late"' . ($is_late ? 'checked' : '') . '>';
    $rows .= '</td>';
    $rows .= '<td><input style="margin-left: 20px;" type="radio"'; 
    $rows .= 'name="' . $user->id . '" ';
    $rows .= 'value="absent"' . ((!$is_present && !$is_late) ? 'checked' : '') . '>';
    $rows .= '</td>';
    $rows .= '</tr>';
}

if(array_key_exists('submit', $_POST)) {
    $present = [];
    $absent = [];
    $late = [];
    foreach ($users as $user) {
        $status = $input->get($user->id, False);
        if($status == 'present') {
            array_push($present, $user->id);
        }
        else if ($status == 'late') {
            array_push($late, $user->id);
        }
        else {
            array_push($absent, $user->id);
        }
    }
    $report = new stdClass();
    $report->present = json_encode($present);
    $report->absent = json_encode($absent);
    $report->late = json_encode($late);
    $report->date_created = $date_str;
    $db = JFactory::getDbo();
    if (isset($report_id)) {
        $report->id = $report_id;
        $report->created_by = $report_created_by;
        $db->updateObject('#__attendance_reports', $report, 'id');
        $app->redirect(JRoute::_('index.php?option=com_attendance&view=report&id=' . $report_id));
    }
    else {
        $report->created_by = Factory::getUser()->name;
        $db->insertObject('#__attendance_reports', $report);
        $app->redirect(JRoute::_('index.php?option=com_attendance&view=report&id=' . $db->insertid()));
    }
}
?>

<script>
function search() {
    const input = document.getElementById("search");
    const filter = input.value.toUpperCase();
    const table = document.getElementById("table");
    const tr = table.getElementsByTagName("tr");
    for (let i = 0; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            const text = td.textContent || td.innerText;
            if (text.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}

function clearSearch() {
    let input = document.getElementById("search");
    input.value = '';
    let table = document.getElementById("table");
    let tr = table.getElementsByTagName("tr");
    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            tr[i].style.display = "";
        }
    }
}
</script>

<style>
    .search-bar {
        margin-top: 20px;
        margin-bottom: 20px;
        width: 300px;
    }

    .details {
        background-color: #eaeaea;
        padding: 8px;
        display:inline-block;
    }

    td {
        width:25%;
    }

    .test {
        padding: 200px;
    }
</style>

<h3><?php echo isset($report_id) ? 'Edit' : 'New'; ?> Attendance Report</h3>
<div class="details fw-light">
    <div><b>Date:</b> <?php echo $date_str; ?></div>
    <?php if (isset($report_id)) echo '<div><b>Created By:</b> ' . $report->created_by . '</div>' ; ?>
</div>
<div class="input-group search-bar">
    <input class="form-control" type="text" id="search" onkeyup="search()" placeholder="Search student name"/>
    <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
</div>
<form method="post"> 
    <table class="table" id="table">
        
            <tr>
                <th>Name</th>
                <th>Present</th>
                <th>Late</th>
                <th>Absent</th>
            </tr>
        
        
            <?php echo $rows; ?>
        
    </table>
    <input type="submit" name="submit" value="Submit" class="btn btn-primary"/>
</form>