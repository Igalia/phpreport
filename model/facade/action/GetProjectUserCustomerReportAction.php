<?php

/** File for GetProjectUserCustomerReportAction
 *
 *  This file just contains {@link GetProjectUserCustomerReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');
include_once('phpreport/model/dao/TaskDAO/TaskDAO.php');

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
        $this->init = $projectVO->getInit();
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
