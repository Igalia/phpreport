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


/** File for GetUserAreaHistoriesAction
 *
 *  This file just contains {@link GetUserAreaHistoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaHistoryVO.php');


/** Get User Area Histories Action
 *
 *  This action is used for retrieving the whole Area History related to a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserAreaHistoriesAction extends Action{

    /** The User Login
     *
     * This variable contains the login of the User whose Area Histories we want to retieve.
     *
     * @var string
     */
    private $userLogin;

    /** GetUserAreaHistoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $userLogin the login of the User whose Area Histories we want to retieve.
     */
    public function __construct($userLogin) {
        $this->userLogin=$userLogin;
        $this->preActionParameter="GET_USER_AREA_HISTORIES_PREACTION";
        $this->postActionParameter="GET_USER_AREA_HISTORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Area Histories from persistent storing.
     *
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserDAO();

        $user = $dao->GetByUserLogin($this->userLogin);

        $dao = DAOFactory::getAreaHistoryDAO();

        return $dao->getByUserId($user->getId());

    }

}


/*//Test code;

$action= new GetUserAreaHistoriesAction('jaragunde');
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
