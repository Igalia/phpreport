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


/** File for GetTaskBlockConfigurationAction
 *
 * This file just contains {@link GetTaskBlockConfigurationAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** GetTaskBlockConfigurationAction Action
 *
 * This action returns all the values implicated in the configuration of task
 * block by date.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class GetTaskBlockConfigurationAction extends Action {

    /** GetTaskBlockConfigurationAction constructor.
     *
     * This is just the constructor of this action.
     */
    public function __construct() {
        $this->preActionParameter="GET_TASK_BLOCK_CONFIGURATION_PREACTION";
        $this->postActionParameter="GET_TASK_BLOCK_CONFIGURATION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that checks the configuration
     * on the DB.
     *
     * @return array "enabled" returns wether task block is enabled or not.
     *         "numberOfDays" returns the number of days configured as time
     *         limit.
     */
    protected function doExecute() {
        $configDao = DAOFactory::getConfigDAO();
        return $configDao->getTaskBlockConfiguration();
    }

}
