<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for SetTaskBlockConfigurationAction
 *
 * This file just contains {@link SetTaskBlockConfigurationAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** SetTaskBlockConfigurationAction Action
 *
 * This action is used to change PhpReport configuration to allow or prevent
 * writing tasks based on the date of those tasks.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class SetTaskBlockConfigurationAction extends Action {

    /** Enabled/disabled value
     *
     * Enable of disable the task block feature.
     *
     * @var boolean
     */
    private $enabled;

    /** Number of days
     *
     * Set the number of days in the past when tasks tasks cannot be altered.
     *
     * @var int
     */
    private $numberOfDays;

    /** SetTaskBlockConfigurationAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param boolean $enabled Enable of disable the task block feature.
     * @param int $numberOfDays Set the number of days in the past when tasks
     *        tasks cannot be altered.
     */
    public function __construct($enabled, $numberOfDays) {
        $this->enabled = $enabled;
        $this->numberOfDays = $numberOfDays;
        $this->preActionParameter = "SET_TASK_BLOCK_CONFIGURATION_PREACTION";
        $this->postActionParameter = "SET_TASK_BLOCK_CONFIGURATION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that attemps to save the configuration in the DB.
     *
     * @return boolean returns wether changes were saved or not.
     */
    protected function doExecute() {
        $configDao = DAOFactory::getConfigDAO();
        return $configDao->setTaskBlockConfiguration($this->enabled,
                $this->numberOfDays);
    }

}
