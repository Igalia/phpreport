<?php

/** File for GetUserProjectCustomerReportAction
 *
 *  This file just contains {@link GetUserProjectCustomerReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Get User Project Customer report Action
 *
 *  This action is used for retrieving information about worked hours in Tasks done by a User for each Project and Customer.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserProjectCustomerReportAction extends Action{

    /** The User.
     *
     * This variable contains the User whose Tasks report we want to retrieve.
     *
     * @var UserVO
     */
    private $userVO;

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

    /** GetUserProjectCustomerReportAction constructor.
     *
     * This is just the constructor of this action. We can pass dates with optional parameters <var>$init</var> and <var>$end</var>
     * if we want to retrieve information about only an interval.
     *
     * @param DateTime $init the initial date of the interval whose Tasks report we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Tasks report we want to retrieve.
     */
    public function __construct(UserVO $userVO, DateTime $init = NULL, DateTime $end = NULL) {

        $this->userVO = $userVO;

        if (is_null($init))
            $this->init =  date_create("1900-01-01");
        else
            $this->init = $init;

        if (is_null($end))
            $this->end =  new DateTime();
        else
            $this->end = $end;

        $this->preActionParameter="GET_USER_PROJECTS_CUSTOMERS_REPORT_PREACTION";
        $this->postActionParameter="GET_USER_PROJECTS_CUSTOMERS_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Tasks report.
     *
     * @return array an associative array with the worked hours data, with the Project description as first level key and
     * the Customer id as second level one.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        $doubleResults = $dao->getTaskReport($this->userVO, $this->init, $this->end, "PROJECT", "CUSTOMER");

        foreach($doubleResults as $doubleResult)
        {

            $results[$doubleResult[projectid]][$doubleResult[customerid]] = $doubleResult[add_hours];

        }

        return $results;

    }

}


/*//Test code;

$user = new UserVO();

$user->setId(58);

$action= new GetUserProjectsCustomersReportAction($user);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
