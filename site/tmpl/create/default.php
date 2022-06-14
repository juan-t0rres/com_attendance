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
use Joomla\CMS\Date\Date;

// Get URL parameters editing an attendance report.
$uri = Uri::getInstance();
$report_id = $uri->getVar('id');
$report_created_by = NULL;
$report_date_created = NULL;

// Joomla APIs for date and input
$app = Factory::getApplication();
$date = Factory::getDate();
$input = $app->input;

// Students are group id 11, fetch all students
$group_id = 11;
$access = new JAccess();
$members = $access->getUsersByGroup($group_id);

// If report_id is set, then we are editing a report
// so we fetch all the data involved.
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
    $report_date_created = new Date($report->date_created);
    $report_date_created = $report_date_created->format('Y-m-d');
    $present_ids = json_decode($report->present);
    $late_ids = json_decode($report->late);
    $absent_ids = json_decode($report->absent);
    $members = array_merge($present_ids, $absent_ids, $late_ids);
    asort($members);
}

// We need a list of all the users that were a part of the report.
$users = [];
foreach ($members as $id) {
    $user = JFactory::getUser($id);
    array_push($users, $user);
}
usort($users, function ($user_a, $user_b) {
    return strcmp($user_a->name, $user_b->name);
});

// Creates/updates the report once the submit button is pressed.
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
    $report->date_created = $input->get('date_created', $date->format('Y-m-d'));
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
// Client side search function to find students easily.
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
        display: inline-block;
    }

    .table {
        background: #fff;
        border-radius: 5px;
    }

    td {
        width:25%;
    }
</style>

<h3><?php echo isset($report_id) ? 'Edit' : 'New'; ?> Attendance Report</h3>
<form method="post"> 
    <div class="details fw-light">
        <div>
        <?php if (isset($report_id)) echo '<span class="fw-bold">Created By</span><p>' . $report->created_by . '</p>' ; ?>
        </div>
        <label class="fw-bold">Date</label>
        <input name="date_created" value="<?php echo isset($report_date_created) ? $report_date_created : $date->format('Y-m-d'); ?>" class="form-control" type="date"/>
    </div>
    <div class="input-group search-bar">
        <input class="form-control" type="text" id="search" onkeyup="search()" placeholder="Search student name"/>
        <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
    </div>
    <table class="table" id="table">
        <tr>
            <th>Name</th>
            <th>Present</th>
            <th>Late</th>
            <th>Absent</th>
        </tr>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?= $user->name; ?></td>
            <td>
                <input 
                    style="margin-left: 20px;" 
                    type="radio" 
                    name="<?= $user->id; ?>"
                    value="present"
                    <?php if(in_array($user->id, $present_ids)) echo 'checked'; ?>
                />
            </td>
            <td>
                <input 
                    style="margin-left: 20px;" 
                    type="radio" 
                    name="<?= $user->id; ?>"
                    value="late"
                    <?php if(in_array($user->id, $late_ids)) echo 'checked'; ?>
                />
            </td>
            <td>
                <input 
                    style="margin-left: 20px;" 
                    type="radio" 
                    name="<?= $user->id; ?>"
                    value="absent"
                    <?php if(!in_array($user->id, $present_ids) && !in_array($user->id, $late_ids)) echo 'checked'; ?>
                />
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <input type="submit" name="submit" value="Submit" class="btn btn-primary"/>
</form>