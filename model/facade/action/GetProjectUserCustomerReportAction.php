<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
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


/** File for GetProjectUserCustomerReportAction
 *
 *  This file just contains {@link GetProjectUserCustomerReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/TaskDAO.php');

/** Get Project User Customer report Action
 *
 *  This action is used for retrieving information about worked hours in Tasks related to a Project, grouped by User and Customer.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectUserCustomerReportAction extends Action{

    /** The Project.
     *
     * This variable contains the Project whose Tasks report we want to retrieve.
     *
     * @var ProjectVO
     */
    private $projectVO;

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

    /** GetProjectUserCustomerReportAction constructor.
     *
     * This is just the constructor of this action. We can pass dates with optional parameters <var>$init</var> and <var>$end</var>
     * if we want to retrieve information about only an interval.
     *
     * @param ProjectVO $projectVO the Project whose Tasks report we want to retrieve.
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     */
    public function __construct(ProjectVO $projectVO, DateTime $init = NULL, DateTime $end = NULL) {
        $this->projectVO=$projectVO;

    if (is_null($init))
        $this->init = date_create('1900-01-01');
    else    $this->init = $init;

        if (is_null($end))
        $this->end = new DateTime();
    else    $this->end = $end;

        $this->preActionParameter="GET_PROJECT_USER_CUSTOMER_REPORT_PREACTION";
        $this->postActionParameter="GET_PROJECT_USER_CUSTOMER_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Tasks reports.
     *
     * @return array an associative array with the worked hours data, with the User login as first level key and the Customer id
     * as second level one.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        $dao2 = DAOFactory::getUserDAO();

        $doubleResults = $dao->getTaskReport($this->projectVO, $this->init, $this->end, "CUSTOMER", "USER");

        foreach ($doubleResults as $doubleResult)
        {

            $user = $dao2->getById($doubleResult['usrid']);

            $results[$user->getLogin()][$doubleResult['customerid']] =  $doubleResult['add_hours'];

        }

        return $results;

    }

}


/*//Test code;

$dao = DAOFactory::getProjectDAO();

$project = $dao->getById(190);

$action= new GetProjectUserCustomerReportAction($project, date_create('2003-11-24'), date_create('2009-01-01'));
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
