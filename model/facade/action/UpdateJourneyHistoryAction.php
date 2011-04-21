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


/** File for UpdateJourneyHistoryAction
 *
 *  This file just contains {@link UpdateJourneyHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/JourneyHistoryVO.php');

/** Update Journey History entry Action
 *
 *  This action is used for updating an entry on Journey History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateJourneyHistoryAction extends Action{

    /** The Journey History
     *
     * This variable contains the Journey History entry we want to update.
     *
     * @var UserVO
     */
    private $journeyHistory;

    /** UpdateJourneyHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param JourneyHistoryVO $journeyHistory the Journey History value object we want to update.
     */
    public function __construct(JourneyHistoryVO $journeyHistory) {
        $this->journeyHistory=$journeyHistory;
        $this->preActionParameter="UPDATE_JOURNEY_HISTORY_PREACTION";
        $this->postActionParameter="UPDATE_JOURNEY_HISTORY_POSTACTION";

    }

    /** Specific code exUpdateJourneyHistoryActionecute.
     *
     * This is the function that contains the code that updates the Journey History entry on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getJourneyHistoryDAO();
        if ($dao->update($this->journeyHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$journeyhistoryvo= new JourneyHistoryVO();
$journeyhistoryvo->setJourney(5);
$journeyhistoryvo->setId(1);
$journeyhistoryvo->setUserId(1);
$journeyhistoryvo->setInitDate(date_create("2009-01-01"));
$journeyhistoryvo->setEndDate(date_create("2009-06-01"));
$action= new UpdateJourneyHistoryAction($journeyhistoryvo);
var_dump($action);
$action->execute();
var_dump($journeyhistoryvo);
*/
