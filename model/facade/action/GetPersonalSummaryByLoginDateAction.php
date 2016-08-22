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

    /** The current active User Goal
     * @var UserGoalVO
     */
    private $currentUserGoal;


    /** GetPersonalSummaryByUserIdDateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $userVO the User whose summary we want to retrieve.
     * @param DateTime $date the date on which we want to compute the summary.
     */
    public function __construct(UserVO $userVO, DateTime $date) {
        $dao = DAOFactory::getJourneyHistoryDAO();
        $userGoaldao = DAOFactory::getUserGoalDAO();

        $this->userVO = $userVO;
        $this->date = $date;
        $this->currentJourney = $dao->getByIntervals( $this->date, $this->date, $this->userVO->getId())[0];
        $this->currentUserGoal = $userGoaldao->getUserGoalsForCurrentDate( $this->userVO->getId(), $this->date );

        if( !$this->currentUserGoal && $this->currentJourney ) {
            $userGoalVO = new UserGoalVO();
            $userGoalVO->setUserId( $userVO->getId() );
            $userGoalVO->setExtraHours( 0 );
            $userGoalVO->setInitDate( max( $this->currentJourney->getInitDate(),
                DateTime::createFromFormat( 'Y-m-d', date('Y-m-d', strtotime('first Monday of January', $this->date->getTimestamp())))));
            $userGoalVO->setEndDate( min( $this->currentJourney->getEndDate(),
                DateTime::createFromFormat( 'Y-m-d', date('Y-m-d', strtotime('last day of December', $this->date->getTimestamp())))));
            $this->currentUserGoal = $userGoalVO;
        }
        $this->preActionParameter="GET_PERSONAL_SUMMARY_BY_USER_LOGIN_DATE_PREACTION";
        $this->postActionParameter="GET_PERSONAL_SUMMARY_BY_USER_LOGIN_DATE_POSTACTION";

    }

    /**
     * @return mixed
     * @throws null
     */
    private function getWorkableHoursInThisPeriod() {
        $initDate = $this->currentUserGoal ?
            max(
                $this->currentJourney->getInitDate(), $this->currentUserGoal->getInitDate()
            ) : $this->currentJourney->getInitDate();
        $endDate = $this->currentUserGoal ?
            min(
                $this->currentJourney->getEndDate(), $this->currentUserGoal->getEndDate()
            ) : $this->currentJourney->getEndDate();

        //Now need to find out the workable hours in this year
        $extraHoursAction = new ExtraHoursReportAction($initDate, $endDate, $this->userVO);
        $results = $extraHoursAction->execute();
        return $results[1][$this->userVO->getLogin()]['workable_hours'];

    }

    /**
     * @return float
     */
    private function getWeeksTillEndOfJourneyPeriod() {
        if ( $this->currentUserGoal ) {
            $endDate = min($this->currentJourney->getEndDate(), $this->currentUserGoal->getEndDate());
        }

        // Its always better to find the difference in weeks from the start of the week, rather than in between
        $thisWeekInitDay = DateTime::createFromFormat( 'Y-m-d', date('Y-m-d', strtotime('last sunday', $this->date->getTimestamp())));
        $lastWeekInitDay = DateTime::createFromFormat( 'Y-m-d', date('Y-m-d', strtotime('next monday', $endDate->getTimestamp())));

        $interval = $thisWeekInitDay->diff( $lastWeekInitDay );
        return floor($interval->days/7);
    }

    /**
     * @param DateTime $initDate
     * @param DateTime $endDate
     * @return float
     */
    private function getWeeksInBetweenDates(DateTime $initDate, DateTime $endDate) {
        $interval = $initDate->diff( $endDate );
        $weeksInBetween = ceil($interval->days/7);

        if ( $weeksInBetween == 0 ) {
            return 1;
        }
        return $weeksInBetween;
    }

    /**
     * @return float
     * @throws null
     */
    private function getWorkedHoursInThisPeriod() {
        $initDay = $this->currentUserGoal ?
            max(
                $this->currentJourney->getInitDate(), $this->currentUserGoal->getInitDate()
            ) : $this->currentJourney->getInitDate();

        $lastWeekInitDay = DateTime::createFromFormat( 'Y-m-d', date('Y-m-d', strtotime('last sunday', $this->date->getTimestamp())));

        $extraHoursAction = new ExtraHoursReportAction($initDay , $lastWeekInitDay , $this->userVO);
        $results = $extraHoursAction->execute();
        return floor($results[1][$this->userVO->getLogin()]['total_hours']);
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
            $extraGoalHoursSet = 0;
            if ( $this->currentUserGoal ) {
                $extraGoalHoursSet += $this->currentUserGoal->getExtraHours() / $this->getWeeksTillEndOfJourneyPeriod();
            }

            $originalHoursToBeWorked = ($this->getWorkableHoursInThisPeriod() - $this->getWorkedHoursInThisPeriod())/ $this->getWeeksTillEndOfJourneyPeriod();
            $totalResults['weekly_goal'] = round( ( $originalHoursToBeWorked + $extraGoalHoursSet ) * 60);
        } else {
            $totalResults['weekly_goal'] = 0;
        }
        return $totalResults;

    }

}