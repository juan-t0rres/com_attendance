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
use Joomla\CMS\Uri\Uri;

// get query params for pagination and date
$uri = Uri::getInstance();
$page = $uri->getVar('page') ?? 1;
$date_param = $uri->getVar('date');

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('id,present,absent,date_created,created_by');
$query->from('#__attendance_reports');
if (isset($date_param)) {
    $date_arr = explode('-', $date_param);
    $date_search = $date_arr[1] . '/' . $date_arr[2] . '/' . $date_arr[0];
    $query->where('date_created LIKE ' . $db->quote($date_search));
}

$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();

$page_size = 5;
$num_pages = $num_rows / $page_size;
$num_pages_float = $num_rows / (float) $page_size;
if ($num_pages_float > $num_pages) {
    $num_pages++;
}

$next_page_href = JURI::current() . '?view=home&page=' . ($page + 1);
$prev_page_href = JURI::current() . '?view=home&page=' . ($page - 1);
if (isset($date_param)) {
    $next_page_href .= '&date=' . $date_param;
    $prev_page_href .= '&date=' . $date_param;
}
$next_page_button = '<li class="page-item"><a class="page-link" href="' . $next_page_href . '">&raquo;</a></li>';
$prev_page_button = '<li class="page-item"><a class="page-link" href="' . $prev_page_href . '">&laquo;</a></li>';
if ($page <= 1) {
    $prev_page_button = null;
}
if ($page >= $num_pages) {
    $next_page_button = null;
}

$query->order('id DESC');
$query->setLimit($page_size, ($page - 1) * $page_size);
$db->setQuery((string) $query);
$reports = $db->loadObjectList();

$rows = '';
if ($reports) {
    foreach ($reports as $report)
    {
        $view_href = JURI::current() . '?view=report&id=' . $report->id;
        $edit_href = JURI::current() . '?view=create&id=' . $report->id;
        $rows .= '<tr>';
        $rows .= '<td>' . $report->date_created . '</td>';
        $rows .= '<td>' . $report->created_by . '</td>';
        $rows .= '<td>';
        $rows .= '<a class="btn btn-primary btn-sm" href="' . $view_href . '" role="button">View</a>';
        $rows .= '<a class="btn btn-secondary btn-sm ms-1" href="' . $edit_href . '" role="button">Edit</a>';
        $rows .= '</td>';
        $rows .= '</tr>';
    }
}

if(array_key_exists('search', $_POST)) {
    $app = Factory::getApplication();
    $input = $app->input;
    $app->redirect(JRoute::_('index.php?option=com_attendance&view=home&page=' . $page . '&date=' . $input->get('date_param')));
}

?>

<style>
    .search-bar {
        margin-top: 20px;
        margin-bottom: 20px;
        width: 400px;
    }

    .pagination {
        margin-left: 0px;
    }
</style>

<h2>Attendance</h2>
<a class="btn btn-primary" href="<?php echo JURI::current(); ?>?view=create">Take Attendance</a>
<form method="post"> 
    <div class="search-bar input-group"> 
        <input name="date_param" class="form-control" value="<?php echo $date_param ?>" type="date"/>
        <input type="submit" name="search" value="Search" class="btn btn-secondary" />
        <a class="btn btn-secondary" href="<?php echo JURI::current(); ?>?view=home">Clear</a>
    </div>
</form>


<table class="table" id="table">
    <tr>
        <th>Date</th>
        <th>Created By</th>
        <th>Action</th>
    </tr>
    <?php echo $rows; ?>
</table>
<ul class="pagination">
    <?php echo $prev_page_button; ?>
    <?php 
        if ($num_pages > 1) {
            echo '<li class="page-item page-link">' . $page . '</li>';
        }
    ?>
    <?php echo $next_page_button; ?>
</ul>
