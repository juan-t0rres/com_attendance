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
$date_str = $date->format('m/d/Y');
$input = $app->input;

$group_id = 11;
$access = new JAccess();
$members = $access->getUsersByGroup($group_id);

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
    $report->date_created = $date_str;
    $db = JFactory::getDbo();
    $db->insertObject('#__attendance_reports', $report);
    $app->redirect(JRoute::_('index.php?option=com_attendance&view=home'));
}
?>

<script>
function search() {
    let input = document.getElementById("search");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("table");
    let tr = table.getElementsByTagName("tr");
    for (let i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
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
</style>

<h2>New Attendance Report</h2>
<h3><?php echo $date_str; ?></h3>
<div class="input-group search-bar">
    <input class="form-control" type="text" id="search" onkeyup="search()" placeholder="Search student name"/>
    <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
</div>
<form method="post"> 
    <table class="table" id="table">
        <tr>
            <th>Name</th>
            <th>Present</th>
        </tr>
        <?php echo $rows; ?>
    </table>
    <input type="submit" name="submit" value="Submit" class="btn btn-primary"/>
</form>