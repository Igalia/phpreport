<?php

/** File for GetUserCityHistoriesAction
 *
 *  This file just contains {@link GetUserCityHistoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CityHistoryVO.php');


/** Get User City Histories Action
 *
 *  This action is used for retrieving the whole City History related to a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserCityHistoriesAction extends Action{

    /** The User Login
     *
     * This variable contains the login of the User whose City Histories we want to retieve.
     *
     * @var string
     */
    private $userLogin;

    /** GetUserCityHistoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User whose City History entries we want to retieve.
     */
    public function __construct($userLogin) {
        $this->userLogin=$userLogin;
        $this->preActionParameter="GET_USER_CITY_HISTORIES_PREACTION";
        $this->postActionParameter="GET_USER_CITY_HISTORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the City Histories from persistent storing.
     *
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserDAO();

        $user = $dao->GetByUserLogin($this->userLogin);

        $dao = DAOFactory::getCityHistoryDAO();

        return $dao->getByUserId($user->getId());

    }

}


/*//Test code;

$action= new GetUserCityHistoriesAction('jaragunde');
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
