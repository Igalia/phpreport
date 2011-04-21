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


/** File for GetPendingHolidayHoursAction
 *
 *  This file just contains {@link GetPendingHolidayHoursAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** Get pending Holiday Hours Action
 *
 *  This action is used for retrieving pending holiday hours for Users.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetPendingHolidayHoursAction extends Action{

    /** The User.
     *
     * This variable contains the User whose pending holiday hours we want to compute.
     *
     * @var ProjectVO
     */
    private $user;

    /** The date interval init.
     *
     * This variable contains the initial date of the interval whose pending holiday hours information we want to compute.
     *
     * @var DateTime
     */
    private $init;

    /** The date interval end.
     *
     * This variable contains the ending date of the interval whose pending holiday hours information we want to compute.
     *
     * @var DateTime
     */
    private $end;

    /** GetPendingHolidayHoursAction constructor.
     *
     * This is just the constructor of this action. We can pass a user with optional parameter <var>$user</var> if we want to compute only its pending holiday hours.
     *
     * @param DateTime $init the initial date of the interval whose pending holiday hours we want to retrieve.
     * @param DateTime $end the ending date of the interval whose pending holiday hours we want to retrieve.
     * @param UserVO $user the User whose pending holiday hours we want to retrieve.
     */
    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL) {
    $this->init = $init;
    $this->end = $end;
    $this->user = $user;
        $this->preActionParameter="GET_PENDING_HOLIDAY_HOURS_PREACTION";
        $this->postActionParameter="GET_PENDING_HOLIDAY_HOURS_POSTACTION";
    }


    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Tasks reports.
     *
     * @return array an associative array with the number of pending holiday hours related to each User's login.
     */
    protected function doExecute() {

    $taskDao = DAOFactory::getTaskDAO();
    $journeyHistoryDao = DAOFactory::getJourneyHistoryDAO();
    $userDao = DAOFactory::getUserDAO();

    // If no User was specified, then we retrieve all
    if (is_null($this->user))
    {
        $groupDAO = DAOFactory::getUserGroupDAO();
        $users = $groupDAO->getUsersByUserGroupName(ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
    }
    else
    {

        // The User can be identified by either the id or the login
        if (is_null($this->user->getLogin()))
        {
            if (!is_null($this->user->getId()))
                $this->user = $userDao->getById($this->user->getId());
        }
        else
            if (is_null($this->user->getId()))
                $this->user = $userDao->getByUserLogin($this->user->getLogin());

        $users[] = $this->user;
    }

    // We compute the holiday hours for each User
    foreach((array)$users as $userVO)
    {

        // We only compute it for workers, so they must be in a group
        if (!is_null($userVO->getGroups()))
        {

            // We get his/her journeys in the date interval
            $journeyHistory = $journeyHistoryDao->getByIntervals($this->init, $this->end, $userVO->getId());

            // He/she starts with no worked hours
            $workHours = 0;

            foreach((array) $journeyHistory as $journeyRow)
            {

                // First of all, we clip the interval with the journey
                $init = $journeyRow->getInitDate();
                $end = $journeyRow->getEndDate();

                if ($init<$this->init)
                    $init = $this->init;

                if ($end>$this->end)
                    $end = $this->end;

                // We get the difference in days...
                $diffJourney = $init->diff($end);
                // and with it and the journey, the worked hours (plus one day because it's a closed ending interval)
                $workHours += ($diffJourney->days + 1)*($journeyRow->getJourney());


                // We must check for leap years on the interval
                $initYear = $init->format("Y");

                // Go from the init year to the end one
                while ($initYear <= $end->format("Y"))
                {

                    // We check if the year is a leap one, and if february 29th is in the interval. There can be some useless checkings here
                    // (february 29th can be prior to the init only in the first year, and later than the end on the last one), but it's no
                    // much computation anyway, and the code is clear (and it's already hard to understand at first)
                    if (checkdate(02,29,$initYear))
                        if ( ($init < date_create($initYear . "-02-29")) && ($end >= date_create($initYear . "-02-29")))
                            $workHours -= $journeyRow->getJourney();        // It's a leap year, so we subtract one journey for february 29th

                    $initYear++;
                }

            }

            // We get the vacations he/she has spent in the interval
            $vacations = $taskDao->getVacations($userVO, $this->init, $this->end);

            // Yearly holiday hours is the standard for an 8-hour journey over a year, so the result is proportional
            $holidayHours = ($workHours/(365*8))*ConfigurationParametersManager::getParameter('YEARLY_HOLIDAY_HOURS');

            // The difference is the number of pending holiday hours
            $userPendingHolidayHours[$userVO->getLogin()]=$holidayHours-$vacations["add_hours"];

        }

    }

    return $userPendingHolidayHours;

    }

}

/*//Uncomment these lines in order to do a simple test of the Action

error_reporting('PHP_ERROR');

$dao = DAOFactory::getUserDAO();

$init = "2009-01-01";
$end = "2009-12-31";

$user = $dao->getByUserLogin("amaneiro");

$groupDAO = DAOFactory::getUserGroupDAO();

if (is_null($user))
    $users = $groupDAO->getUsersByUserGroupName(ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
else
    $users[] = $user;

$init = date_create($init);
$end = date_create($end);

$action= new GetPendingHolidayHoursAction($init, $end, $user);

$pendingHours = $action->execute();

foreach($users as $k)
{
    print "\nUser: " . $k->getLogin() . "\n";
    print "Pending holiday hours: " . $pendingHours[$k->getLogin()] . "\n";
}
*/
