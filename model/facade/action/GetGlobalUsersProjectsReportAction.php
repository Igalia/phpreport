<?php

/** File for GetGlobalUsersProjectsReportAction
 *
 *  This file just contains {@link GetGlobalUsersProjectsReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');

/** Get Global Users Projects Report Action
 *
 *  This action is used for retrieving information about worked hours in Tasks done by Users for each Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetGlobalUsersProjectsReportAction extends Action{

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

    /** GetGlobalUsersProjectsReportAction constructor.
     *
     * This is just the constructor of this action. We can pass dates with optional parameters <var>$init</var> and <var>$end</var>
     * if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     */
    public function __construct(DateTime $init = NULL, DateTime $end = NULL) {
        if (is_null($init))
        $this->init =  date_create("1900-01-01");
    else
        $this->init = $init;

    if (is_null($end))
        $this->end =  new DateTime();
    else
        $this->end = $end;

        $this->preActionParameter="GET_GLOBAL_USERS_PROJECTS_REPORT_PREACTION";
        $this->postActionParameter="GET_GLOBAL_USERS_PROJECTS_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Tasks reports.
     *
     * @return array an array with the resulting rows of computing the worked hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields <i>userid</i> and <i>projectid</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskDAO();

        return $dao->getGlobalTaskReport($this->init, $this->end, "PROJECT");

    }

}


/*//Test code;

$action= new GetGlobalUsersProjectsReportAction();
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
