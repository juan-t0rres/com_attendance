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

//deletes all attendance records when the red button is pressed 
if(isset($_POST['deleteAttendanceRecords']))
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query = "DELETE FROM #__attendance_reports";
    $db->setQuery($query)->execute();
    echo 'The attendance records have been deleted';
}
?>

<html>
    <body>
        <br><br>
        <h2>Delete Attendance Records</h2>

        <p>This button will <b>delete all attendance records</b> currently in the system</p>
        <p>BE VERY CAREFUL!</p>
        
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="checkboxConfirm" onchange="document.getElementById('deleteAttendanceRecords').disabled = !this.checked;">
            <label class="form-check-label" for="checkboxConfirm">Yes, I want to delete all attendance records</label>
        </div>

        <br>

        <form method="post">
            <input class="btn btn-danger" type="submit" name="deleteAttendanceRecords" id="deleteAttendanceRecords" value="Delete All Attendance Records" disabled/>
        </form>
    </body>
</html>
