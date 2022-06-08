<?php

namespace JuanTorres\Component\Attendance\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_attendance
 *
 * @copyright   Copyright (C) 2020 John Smith. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Default Controller of Attendance component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_attendance
 */
class DisplayController extends BaseController {
    /**
     * The default view for the display method.
     *
     * @var string
     */
    protected $default_view = 'home';
    
    public function display($cachable = false, $urlparams = array()) {
        return parent::display($cachable, $urlparams);
    }
    
}