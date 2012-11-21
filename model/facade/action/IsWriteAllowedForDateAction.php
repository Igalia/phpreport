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


/** File for IsWriteAllowedForDateAction
 *
 * This file just contains {@link IsWriteAllowedForDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** IsWriteAllowedForDateAction Action
 *
 * This action is used to know the status of the configuration regarding the
 * ability to save tasks on a specific date.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class IsWriteAllowedForDateAction extends Action {

    /** The Date
     *
     * This variable contains the date to check on the configuration.
     *
     * @var DateTime
     */
    private $task;

    /** IsWriteAllowedForDateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param DateTime $date the date to check on the configuration.
     */
    public function __construct(DateTime $date) {
        $this->date=$date;
        $this->preActionParameter="IS_WRITE_ENABLED_FOR_DATE_PREACTION";
        $this->postActionParameter="IS_WRITE_ENABLED_FOR_DATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that checks the configuration
     * on the DB.
     *
     * @return boolean true if task save is enabled for that date, false
     *         otherwise.
     * @throws {@link SQLQueryErrorException}
     */
    protected function doExecute() {
        $configDao = DAOFactory::getConfigDAO();
        return $configDao->isWriteAllowedForDate($this->date);
    }

}
