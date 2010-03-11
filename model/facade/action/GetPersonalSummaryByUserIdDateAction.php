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


/** File for GetPersonalSummaryByUserIdDateAction
 *
 *  This file just contains {@link GetPersonalSummaryByUserIdDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Get Personal Work Summary by User Id and Date Action
 *
 *  This action is used for retrieving data about work done by a User on a date,
 *  its week and its month by his/her user Id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetPersonalSummaryByUserIdDateAction extends Action{

    /** The User Id
     *
     * This variable contains the id of the User whose summary we want to
     * obtain.
     *
     * @var int
     */
    private $userId;

    /** The date
     *
     * This variable contains the date on which we want to compute the summary.
     *
     * @var DateTime
     */
    private $date;

    /** GetPersonalSummaryByUserIdDateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User whose summary we want to retrieve.
     * @param DateTime $date the date on which we want to compute the summary.
     */
    public function __construct($userId, DateTime $date) {
        $this->userId = $userId;
        $this->date = $date;
        $this->preActionParameter="GET_PERSONAL_SUMMARY_BY_USER_ID_DATE_PREACTION";
        $this->postActionParameter="GET_PERSONAL_SUMMARY_BY_USER_ID_DATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that obtains the summary.
     *
     * @return array an array with the values related to the keys 'day', 'week' and 'month'.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        return $dao->getPersonalSummary($this->userId, $this->date);

    }

}

/*//Test code

$action = new GetPersonalSummaryByUserIdDateAction(60, date_create('2009-12-01'));
//var_dump($action);
$action->execute();
var_dump($action);
*/
