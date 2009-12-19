<?php

/** File for GetUserHourCostHistoriesAction
 *
 *  This file just contains {@link GetUserHourCostHistoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/HourCostHistoryVO.php');


/** Get User Hour Cost Histories Action
 *
 *  This action is used for retrieving the whole Hour Cost History related to a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserHourCostHistoriesAction extends Action{

    /** The User Login
     *
     * This variable contains the login of the User whose Hour Cost History entries we want to retieve.
     *
     * @var string
     */
    private $userLogin;

    /** GetUserHourCostHistoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $userLogin the login of the User whose Hour Cost Histories we want to retieve.
     */
    public function __construct($userLogin) {
        $this->userLogin=$userLogin;
        $this->preActionParameter="GET_USER_HOUR_COST_HISTORIES_PREACTION";
        $this->postActionParameter="GET_USER_HOUR_COST_HISTORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Hour Cost Histories from persistent storing.
     *
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserDAO();

        $user = $dao->GetByUserLogin($this->userLogin);

        $dao = DAOFactory::getHourCostHistoryDAO();

        return $dao->getByUserId($user->getId());

    }

}


/*//Test code;

$action= new GetUserHourCostHistoriesAction('jaragunde');
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
