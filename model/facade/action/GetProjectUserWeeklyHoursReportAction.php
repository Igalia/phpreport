<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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


/** File for GetProjectUserWeeklyHoursReportAction
 *
 *  This file just contains {@link GetProjectUserWeeklyHoursReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Tony Thomas <tthomas@igalia.com>
 */
include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** Get Users Weekly Hours Report Action
 *
 *  This action is used for retrieving information about work done on a given Project
 *  by all Users per week.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Tony Thomas <tthomas@igalia.com>
 */
class GetProjectUserWeeklyHoursReportAction extends Action{

    /** The date interval init.
     *
     * This variable contains the initial date of the interval whose Tasks information we want to retrieve.
     *
     * @var DateTime
     */
    private $init;

    /** The date interval end.
     *
     * This variable contains the ending date of the interval whose Tasks information we want to retrieve.
     *
     * @var DateTime
     */
    private $end;

    /** GetProjectUserWeeklyHoursReportAction constructor.
     *
     * This is just the constructor of this action. We can pass dates with optional parameters <var>$init</var> and <var>$end</var>
     * if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     */
    public function __construct(ProjectVO $projectVO, DateTime $init = NULL, DateTime $end = NULL) {
        $this->projectVO = $projectVO;
        if (is_null($init))
            $this->init =  date_create("1900-01-01");
        else
            $this->init = $init;

        if (is_null($end))
            $this->end =  new DateTime();
        else
            $this->end = $end;

        $this->preActionParameter="GET_GLOBAL_PROJECTS_USERS_WEEKLY_HOURS_REPORT_PREACTION";
        $this->postActionParameter="GET_GLOBAL_PROJECTS_USERS_WEEKLY_HOURS_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Weekly user activity reports.
     *
     * @return array an associative array with the worked hours data, with the Project name as first level key and the User login
     * as second level one.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        $dao3 = DAOFactory::getUserDAO();

        $doubleResults = $dao->getProjectUserWeeklyWorkingHours($this->projectVO, $this->init, $this->end,  "USER");

        $results = array();

        foreach ($doubleResults as $doubleResult)
        {
            $user = $doubleResult['usrid'];
            $userLogin = $dao3->getById($user);

            $week = $doubleResult['week'];
            $results[$userLogin->getLogin()][$week] =  $doubleResult['total_hours'];

        }

        return $results;
    }

}