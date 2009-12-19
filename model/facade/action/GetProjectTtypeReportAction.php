<?php

/** File for GetProjectTtypeReportAction
 *
 *  This file just contains {@link GetProjectTtypeReportAction}.
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

/** Get Project task type report Action
 *
 *  This action is used for retrieving information about worked hours in Tasks related to a Project, grouped by task type (ttype).
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectTtypeReportAction extends Action{

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

    /** GetProjectTtypeReportAction constructor.
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

        $this->preActionParameter="GET_PROJECT_TTYPE_REPORT_PREACTION";
        $this->postActionParameter="GET_PROJECT_TTYPE_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Tasks reports.
     *
     * @return array an array with the resulting rows of computing the worked hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and field for the grouping field <i>ttype</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskDAO();

    return $dao->getTaskReport($this->projectVO, $this->init, $this->end, "TTYPE");

    }

}


/*//Test code;

$dao = DAOFactory::getProjectDAO();

$project = $dao->getById(138);

$action= new GetProjectTtypeReportAction($project);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
