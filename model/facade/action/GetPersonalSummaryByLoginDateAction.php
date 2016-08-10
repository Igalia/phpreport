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


/** File for GetPersonalSummaryByLoginDateAction
 *
 *  This file just contains {@link GetPersonalSummaryByLoginDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserJourneyHistoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/ExtraHoursReportAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Get Personal Work Summary by Login and Date Action
 *
 *  This action is used for retrieving data about work done by a User on a date,
 *  its week and its month by his/her login (user Id also works).
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetPersonalSummaryByLoginDateAction extends Action{

    /** The User
     *
     * This variable contains the the User whose summary we want to
     * obtain.
     *
     * @var UserVO
     */
    private $userVO;

    /** The date
     *
     * This variable contains the date on which we want to compute the summary.
     *
     * @var DateTime
     */
    private $date;

    /** The current active Journey
     *
     * @var JourneyHistoryVO
     */
    private $currentJourney;


    /** GetPersonalSummaryByUserIdDateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $userVO the User whose summary we want to retrieve.
     * @param DateTime $date the date on which we want to compute the summary.
     */
    public function __construct(UserVO $userVO, DateTime $date) {
        $dao = DAOFactory::getJourneyHistoryDAO();
        $this->userVO = $userVO;
        $this->date = $date;
        $this->currentJourney = $dao->getByIntervals( $this->date, $this->date, $this->userVO->getId())[0];
        $this->preActionParameter="GET_PERSONAL_SUMMARY_BY_USER_LOGIN_DATE_PREACTION";
        $this->postActionParameter="GET_PERSONAL_SUMMARY_BY_USER_LOGIN_DATE_POSTACTION";

    }

    /**
     * @return mixed
     * @throws null
     */
    private function getWorkableHoursInThisJourneyPeriod() {
        $initDate = $this->currentJourney->getInitDate();
        $endDate = $this->currentJourney->getEndDate();

        //Now need to find out the workable hours in this year
        $extraHoursAction = new ExtraHoursReportAction($initDate, $endDate, $this->userVO);
        $results = $extraHoursAction->execute();
        return $results[1][$this->userVO->getLogin()]['workable_hours'];

    }

    /**
     * @return mixed
     * @throws null
     */
    private function getWorkedHoursInThisJourneyPeriod() {
        $initDate = $this->currentJourney->getInitDate();
        $endDate = $this->currentJourney->getEndDate();

        // We need to get the number of hours worked till date
        $extraHoursAction = new ExtraHoursReportAction($initDate , $endDate, $this->userVO);
        $results = $extraHoursAction->execute();
        return $results[1][$this->userVO->getLogin()]['total_hours'];
    }

    /**
     * @return float
     */
    private function getWeeksTillEndOfJourneyPeriod() {
        $lastDayOfJourney = $this->currentJourney->getEndDate();

        $interval = $this->date->diff( $lastDayOfJourney );
        return ceil($interval->days/7);
    }

    /**
     * @param DateTime $initDate
     * @param DateTime $endDate
     * @return float
     */
    private function getWeeksInBetweenDates(DateTime $initDate, DateTime $endDate) {
        $interval = $initDate->diff( $endDate );
        return ceil($interval->days/7);
    }

    /** Specific code execute.
     *
     * This is the function that contains the code that obtains the summary.
     *
     * @return array an array with the values related to the keys 'day', 'week' and 'month'.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        $user = $this->userVO;

        if (is_null($user->getId()))
        {

            $dao2 = DAOFactory::getUserDAO();

            $user = $dao2->getByUserLogin($user->getLogin());

            if (is_null($user))
                return NULL;

        }
        $totalResults = $dao->getPersonalSummary($user->getId(), $this->date);

        if( $this->currentJourney ) {
            $userGoaldao = DAOFactory::getUserGoalDAO();
            $goalsInCurrentDate = $userGoaldao->getUserGoalsForCurrentDate( $this->userVO->getId(), $this->date );

            $extraGoalHoursSet = 0;
            if ( $goalsInCurrentDate ) {
                // Only if some goal is set for this period
                foreach ( $goalsInCurrentDate as $currentUserGoalVO ) {
                    $weeksInBetween = $this->getWeeksInBetweenDates( $currentUserGoalVO->getInitDate(), $currentUserGoalVO->getEndDate() );
                    $extraGoalHoursSet += ( $currentUserGoalVO->getExtraHours() / $weeksInBetween );
                }
            }

            $hoursLeftToBeWorked = $this->getWorkableHoursInThisJourneyPeriod() - $this->getWorkedHoursInThisJourneyPeriod();
            $weeksLeftTillEndOfJourneyPeriod = $this->getWeeksTillEndOfJourneyPeriod();

            $originalHoursToBeWorked = round($hoursLeftToBeWorked / $weeksLeftTillEndOfJourneyPeriod , 2);

            $totalResults['weekly_goal'] = floor( ( $originalHoursToBeWorked + $extraGoalHoursSet ) * 60);
        } else {
            $totalResults['weekly_goal'] = 0;
        }

        //var_dump( $totalResults );
        return $totalResults;

    }

}