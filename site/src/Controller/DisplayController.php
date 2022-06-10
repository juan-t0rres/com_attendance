<?php

namespace JuanTorres\Component\Attendance\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

/**
 * @package     Joomla.Site
 * @subpackage  com_attendance
 *
 * @copyright   Copyright (C) 2020 John Smith. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Attendance Component Controller
 * @since  0.0.2
 */
class DisplayController extends BaseController {
    
    public function display($cachable = false, $urlparams = array()) {        
        $document = Factory::getDocument();
        $document->setTitle('Attendance');
        $app = Factory::getApplication();
        $menu = $app->getMenu();
        $items = $menu->getMenu();
        foreach ($items as $item) {
            if($item->title == 'Attendance') {
                $menu->setActive($item->id);
            }
        }
        $menu->setActive(103);
        $viewName = $this->input->getCmd('view', 'login');
        $viewFormat = $document->getType();
        $view = $this->getView($viewName, $viewFormat);
        $view->document = $document;
        $view->display();
    }
    
}