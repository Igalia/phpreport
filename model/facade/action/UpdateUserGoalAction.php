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


/** File for UpdateUserGoalAction
 *
 *  This file just contains {@link UpdateUserGoalAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');

/** Update User Goal entry Action
 *
 *  This action is used for updating an entry on Area History.
 *
 * @package PhpReport
 * @subpackage facade
 */
class UpdateUserGoalAction extends Action{

    /** The User Goal
     *
     * This variable contains the User Goal entry we want to update.
     *
     * @var UserGoalVO
     */
    private $userGoal;

    /** UpdateUserGoalAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserGoalVO $userGoal the User Goal value object we want to update.
     */
    public function __construct(UserGoalVO $userGoal) {
        $this->userGoal=$userGoal;
        $this->preActionParameter="UPDATE_USER_GOAL_PREACTION";
        $this->postActionParameter="UPDATE_USER_GOAL_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the User Goal entry on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {
        $dao = DAOFactory::getUserGoalDAO();

        if ($dao->update($this->userGoal)!=1) {
            return -1;
        }

        return 0;
    }

}