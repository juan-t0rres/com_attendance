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

?>

<html>
    <body>

        <h2>Delete Attendance Records</h2>

        <p>This button will delete ALL current attendance records in the system.</p>
        <p>BE VERY CAREFUL!</p>

        <script>
            function deleteAttendanceRecords() {
            let text = "Are you sure you want to delete all the attendance records?\nEither OK or Cancel.";
            
            if (confirm(text) == true)
            {
                <?php
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $query = "DELETE FROM #__attendance_reports";
                    $db->setQuery($query)->execute();
                ?>

                text = "The attendance records have been deleted.";
            }
            else
            {
                text = "You canceled.";
            }
            document.getElementById("response").innerHTML = text;
            }
        </script>

        <button class="btn btn-danger" onclick="deleteAttendanceRecords()">Delete All Attendance Records</button>

        <p id="response"></p>

    </body>
</html>
