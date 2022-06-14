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
use Joomla\CMS\Date\Date;

// Get query params for pagination and date.
$uri = Uri::getInstance();
$page = $uri->getVar('page') ?? 1;
$date_param = $uri->getVar('date');
$page_size = $uri->getVar('page_size') ?? 5;

// Start setting up attendance reports query
// Get total number of reports
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__attendance_reports');
if (isset($date_param)) {
    $query->where('date_created LIKE ' . $db->quote($date_param));
}
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();

// Pagination setup.
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
$next_page_href .= '&page_size=' . $page_size;
$prev_page_href .= '&page_size=' . $page_size;

// Pagination options setup.
$page_size_options = [5, 10, 20];
$options = '';
foreach ($page_size_options as $page_size_option) {
    $uri->setVar('page_size', $page_size_option);
    $options .= '<option value="?';
    $options .= $uri->getQuery() . '" ';
    if ($page_size == $page_size_option) {
        $options .= 'selected';
    }
    $options .= '>' . $page_size_option . '</option>';
}

// Make the query to get the reports ordered by date.
$query->order('date_created DESC');
$query->setLimit($page_size, ($page - 1) * $page_size);
$db->setQuery((string) $query);
$reports = $db->loadObjectList();
// Set the date created in the right format.
if ($reports) {
    foreach ($reports as $report) {
        $report_date_created = new Date($report->date_created);
        $report_date_created = $report_date_created->format('m/d/Y');
        $report->date_created = $report_date_created;
    }
}

// Once the search button is clicked, add the query params and redirect.
if(array_key_exists('search', $_POST)) {
    $app = Factory::getApplication();
    $input = $app->input;
    $app->redirect(JRoute::_('index.php?option=com_attendance&view=home&page=' . $page . '&date=' . $input->get('date_param')));
}

?>

<style>
    .table {
        background: #fff;
        border-radius: 5px;
    }

    .filters {
        margin-top: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-bar {
        width: 310px;
    }

    .pagination {
        margin-left: 0px;
    }
</style>

<h2>Attendance</h2>
<a class="btn btn-primary" href="<?php echo JURI::current(); ?>?view=create">Take Attendance</a>
<form method="post" class="filters"> 
    <div class="search-bar input-group"> 
        <input name="date_param" class="form-control" value="<?php echo $date_param ?>" type="date"/>
        <input type="submit" name="search" value="Search" class="btn btn-secondary" />
        <a class="btn btn-secondary" href="<?php echo JURI::current(); ?>?view=home">Clear</a>
    </div>
    <div>
        <label for="page-size-select">Results</label>
        <select id="page-size-select" class="form-control-sm" onchange="location = this.value;">
            <?php echo $options; ?>
        </select>
    </div>
</form>

<table class="table" id="table">
    <tr>
        <th>Date</th>
        <th>Created By</th>
        <th>Action</th>
    </tr>
    <?php foreach($reports as $report): ?>
    <tr>
        <td><?= $report->date_created; ?></td>
        <td><?= $report->created_by; ?></td>
        <td>
            <a class="btn btn-primary btn-sm" href="<?= JURI::current() . '?view=report&id=' . $report->id; ?>" role="button">View</a>
            <a class="btn btn-secondary btn-sm ms-1" href="<?= JURI::current() . '?view=create&id=' . $report->id; ?>" role="button">Edit</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<ul class="pagination">
    <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="<?= $prev_page_href; ?>">&laquo;</a></li>
    <?php endif; ?>
    <?php if ($num_pages > 1): ?>
        <li class="page-item page-link"><?= $page; ?></li>
    <?php endif; ?>
    <?php if ($page < $num_pages): ?>
        <li class="page-item"><a class="page-link" href="<?= $next_page_href; ?>">&raquo;</a></li>
    <?php endif; ?>
</ul>
