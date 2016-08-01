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


/** File for GetUserGoalsAction
 *
 *  This file just contains {@link GetUserGoalsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');


/** Get User Goals Action
 *
 *  This action is used for retrieving the whole User Goals related to a User.
 *
 * @package PhpReport
 * @subpackage facade
 */
class GetUserGoalsAction extends Action{

	/** The User Login
	 *
	 * This variable contains the login of the User whose User Goals we want to retrieve.
	 *
	 * @var string
	 */
	private $userLogin;

	/** GetUserGoalsAction constructor.
	 *
	 * This is just the constructor of this action.
	 *
	 * @param string $userLogin the login name of the User whose User Goal entries we want to retrieve.
	 */
	public function __construct($userLogin) {
		$this->userLogin=$userLogin;
		$this->preActionParameter="GET_USER_GOALS_PREACTION";
		$this->postActionParameter="GET_USER_GOALS_POSTACTION";

	}

	/** Specific code execute.
	 *
	 * This is the function that contains the code that retrieves the User Goals from persistent storing.
	 *
	 * @return array an array with value objects {@link UserGoalVO} with their properties set to the values from the rows
	 * and ordered ascendantly by their database internal identifier.
	 */
	protected function doExecute() {

		$dao = DAOFactory::getUserDAO();

		$user = $dao->GetByUserLogin($this->userLogin);

		$userGoaldao = DAOFactory::getUserGoalDAO();

		return $userGoaldao->getByUserId($user->getId());

	}

}