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


/** File for ExtraHoursReportAction
 *
 *  This file just contains {@link ExtraHoursReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Get Extra Hours report Action
 *
 *  This action is used for retrieving information about Extra Hours done by users.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class ExtraHoursReportAction extends Action {

    /** The User.
     *
     * This variable contains the User whose Extra Hours report we want to retrieve.
     *
     * @var UserVO
     */
    private $user;

    /** The date interval init.
     *
     * This variable contains the initial date of the interval whose Extra Hours we want to retrieve.
     *
     * @var DateTime
     */
    private $init;

    /** The date interval end.
     *
     * This variable contains the ending date of the interval whose Extra Hours we want to retrieve.
     *
     * @var DateTime
     */
    private $end;

    /** ExtraHoursReportAction constructor.
     *
     * This is just the constructor of this action. We can pass a user with optional parameter <var>$user</var> if we want to retrieve only its Extra Hours report.
     *
     * @param DateTime $init the initial date of the interval whose Extra Hours we want to retrieve.
     * @param DateTime $end the ending date of the interval whose Extra Hours we want to retrieve.
     * @param UserVO $user the User whose Extra Hours report we want to retrieve.
     */
    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL) {
        $this->init = $init;
        $this->end = $end;
        $this->user = $user;
        $this->preActionParameter="EXTRA_HOURS_REPORT_PREACTION";
        $this->postActionParameter="EXTRA_HOURS_REPORT_POSTACTION";
    }


    /** Translate to days from epoch.
     *
     * Compute the days passed since a reference date called "epoch" ("1970-01-05").<br/>NOTE: For convenience reasons, the epoch ISN'T THE SAME as the standard unix epoch,
     * because it's Monday and can be used to compute the week day doing modulus.
     *
     * @param DateTime $date the date that we want to translate to days from epoch.
     * @return int the number of days since 1970-01-05.
     */
    private function daysFromEpoch(DateTime $date) {
        $aux = date_create($date->format("Y-m-d"));    // <---- This code acts as a workaround with a PHP bug

        // 1970-01-05 is a better epoch day, because it's Monday and can be
        // used to compute the week day doing modulus

        $aux2 = $aux->diff(date_create("1970-01-05"));
        $result =  $aux2->days;
        return $result;
    }


    /** Compute the number of working days.
     *
     * Compute the number of working days between two given dates (both included).
     *
     * @param DateTime $init the init date of the interval.
     * @param DateTime $end the ending date of the interval.
     * @return int the number of working days between the two dates.
     */
    private function numWorkDays(DateTime $init, DateTime $end) {
        // Taking on account the things said in function num_weekend_days,
        // this is a function that works in a similar way to get the number
        // of *work* days (that means excluding weekends) between two dates.

        // NOTE: -1 because we also include the start date
        // (init<=x<=end, INSTEAD OF init<x<=end)
        $days_epoch_to_init=$this->daysFromEpoch($init)-1;
        $days_epoch_to_end=$this->daysFromEpoch($end);
        $weekend_days_epoch_to_init=floor($days_epoch_to_init/7)*2;
        if ($days_epoch_to_init%7==5) $weekend_days_epoch_to_init+=1;
        else if ($days_epoch_to_init%7==6) $weekend_days_epoch_to_init+=2;

        $weekend_days_epoch_to_end=floor($days_epoch_to_end/7)*2;
        if ($days_epoch_to_end%7==5) $weekend_days_epoch_to_end+=1;
        else if ($days_epoch_to_end%7==6) $weekend_days_epoch_to_end+=2;

        $result = $days_epoch_to_end - $days_epoch_to_init -
            ($weekend_days_epoch_to_end - $weekend_days_epoch_to_init);


        return $result;
    }


    /** Compute the number of extra hours worked
     *
     * Compute the number of extra hours users have worked between two dates (both included).
     * We can pass an optional parameter, <var>$user</var>, if we want to compute only a User data.
     * <br/><br/>It returns an associative array with the following data related to each User's login:
     * <ul>
     * <li>'total_hours': number of total hours a User has worked in the specified interval.</li>
     * <li>'workable_hours': number of hours a User must work in the specified interval according to his/her journey.</li>
     * <li>'extra_hours': number of extra hours a User has worked, according to the previous data.</li></ul>
     *
     * @param DateTime $init the init date of the interval.
     * @param DateTime $end the ending date of the interval.
     * @param UserVO $user the User whose extra hours we want to compute.
     */
    private function netExtraHours(DateTime $init, DateTime $end, UserVO $user = NULL) {

        $taskDao = DAOFactory::getTaskDAO();
        $commonDao = DAOFactory::getCommonEventDAO();
        $journeyHistoryDao = DAOFactory::getJourneyHistoryDAO();
        $cityHistoryDao = DAOFactory::getCityHistoryDAO();

        if (is_null($user))
        {
            $groupDAO = DAOFactory::getUserGroupDAO();
            $users = $groupDAO->getUsersByUserGroupName(
                    ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
        }
        else
        {
            $users = array($user);
        }

        $userWork = array();

        foreach($users as $userVO)
        {
            if (!is_null($userVO->getId()))
            {
                $hoursWorked = $taskDao->
                        getTaskReport($userVO, $init, $end, "USER");

                $journeyHistory = $journeyHistoryDao->
                        getByIntervals($init, $end, $userVO->getId());

                $cityHistory = $cityHistoryDao->
                        getByIntervals($init, $end, $userVO->getId());

                if (is_null($hoursWorked[0]['add_hours']))
                    $userWork[$userVO->getLogin()]["total_hours"] = 0;
                else
                    $userWork[$userVO->getLogin()]["total_hours"] =
                            $hoursWorked[0]['add_hours'];

                $userWork[$userVO->getLogin()]["workable_hours"] = 0;

                $histories = array();

                $i = 0;

                foreach((array) $journeyHistory as $journeyRow)
                    foreach((array) $cityHistory as $cityRow)
                    {

                        $newPeriod = FALSE;

                        // We check if the history entries have a commmon period
                        //(we first check if their end dates are null, so we know what to check next)

                        if (!is_null($journeyRow->getEndDate()))
                            if (!is_null($cityRow->getEndDate()))
                            {
                                if (!(($journeyRow->getInitDate() < $cityRow->getInitDate())
                                        && ($journeyRow->getEndDate() < $cityRow->getInitDate()))
                                        && !(($journeyRow->getInitDate() > $cityRow->getEndDate())
                                        && ($journeyRow->getEndDate() > $cityRow->getEndDate())))
                                    $newPeriod = TRUE;
                            } else
                            {
                                if (!(($journeyRow->getInitDate() < $cityRow->getInitDate())
                                        && ($journeyRow->getEndDate() < $cityRow->getInitDate())))
                                    $newPeriod = TRUE;
                            }

                        else
                        {
                            if (!is_null($cityRow->getEndDate()))
                            {
                                if (!(($cityRow->getInitDate() < $journeyRow->getInitDate())
                                        && ($cityRow->getEndDate() < $journeyRow->getInitDate())))
                                    $newPeriod = TRUE;
                            }
                            else $newPeriod = TRUE;
                        }

                        if ($newPeriod)
                        {
                            $histories[$i]["init"] = $journeyRow->getInitDate();
                            $histories[$i]["end"] = $journeyRow->getEndDate();
                            $histories[$i]["journey"] = $journeyRow->getJourney();

                            if (($histories[$i]["init"] < $cityRow->getInitDate())
                                    && ( is_null($histories[$i]["end"])
                                    || ($histories[$i]["end"] > $cityRow->getInitDate())))
                                $histories[$i]["init"] = $cityRow->getInitDate();

                            if (is_null($histories[$i]["end"])
                                    || (($histories[$i]["end"] > $cityRow->getEndDate()))
                                    && ($histories[$i]["init"] < $cityRow->getEndDate()))
                                $histories[$i]["end"] = $cityRow->getEndDate();

                            // If both dates are NULL, then end date is nowadays

                            if (is_null($histories[$i]["end"]))
                                $histories[$i]["end"] = new DateTime();

                            $histories[$i]["city"] = $cityRow->getCityId();

                            $i++;
                        }
                    }

                $hours = 0;

                foreach((array) $histories as $row)
                {

                    if ($row["init"] < $init)
                        $row["init"] = $init;

                    if ($row["end"] > $end)
                        $row["end"] = $end;

                    $work = $this->numWorkDays($row["init"], $row["end"]);
                    $holidays = count($commonDao->
                            getByCityIdDates($row["city"], $row["init"], $row["end"]));
                    $workHours = ($work - $holidays) * $row["journey"];
                    $userWork[$userVO->getLogin()]["workable_hours"] += $workHours;

                }

                $userWork[$userVO->getLogin()]["extra_hours"] =
                        $userWork[$userVO->getLogin()]["total_hours"]
                        - $userWork[$userVO->getLogin()]["workable_hours"];
            }

        }

        return $userWork;
    }


    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Extra Hours reports.<br/><br/>
     * It returns an associative array with the following data:
     * <ul>
     * <li>Position 0: it has other array with data from all Users:
     * <ul>
     * <li>'total_hours': number of hours all Users have worked in the specified interval.</li>
     * <li>'workable_hours': number of hours all Users must work in the specified interval according to their journey.</li>
     * <li>'extra_hours': number of extra hours all Users have worked, according to the previous data.</li>
     * <li>'total_extra_hours': number of extra hours all Users have worked as for now (not just in the given interval).
     * </ul></li>
     * <li>Position 1: it has other array with data related to each User's login:
     * <ul>
     * <li>'total_hours': number of hours a User has worked in the specified interval.</li>
     * <li>'workable_hours': number of hours a User must work in the specified interval according to his/her journey.</li>
     * <li>'extra_hours': number of extra hours a User has worked, according to the previous data.</li>
     * <li>'total_extra_hours': number of extra hours a User has worked as for now (not just in the given interval).</li>
     * </ul></li></ul>
     *
     * @return array an associative array (it's described above).
     */
    protected function doExecute() {

        $extraHourDao = DAOFactory::getExtraHourDAO();
        $userDAO = DAOFactory::getUserDAO();

        if (is_null($this->user))
        {
            $groupDAO = DAOFactory::getUserGroupDAO();
            $users = $groupDAO->getUsersByUserGroupName(
                    ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
        }
        else
        {
            if (is_null($this->user->getLogin()))
            {
                if (!is_null($this->user->getId()))
                    $this->user = $userDAO->getById($this->user->getId());
            }
            else
                if (is_null($this->user->getId()))
                    $this->user = $userDAO->getByUserLogin($this->user->getLogin());

            $users[] = $this->user;
        }

        $addResults["total_hours"] = 0;
        $addResults["workable_hours"] = 0;
        $addResults["extra_hours"] = 0;
        $addResults["total_extra_hours"] = 0;


        foreach ((array) $users as $user)
        {

            $work = $this->netExtraHours($this->init, $this->end, $user);

            $previous = $extraHourDao->getLastByUserId($user->getId(), $this->end);

            if (!is_null($previous)) {
                if ($previous->getDate() < $this->init)
                    $previousInit= $previous->getDate()->add(new DateInterval("P1D"));
                else
                    $previousInit= $previous->getDate();
                $previousExtraHours[$user->getLogin()]=$previous->getHours();
            } else {
                $previousInit=date_create("1900-01-01");
                $previousExtraHours[$user->getLogin()]=0;
            }

            $auxDate = clone ($this->init);
            if ($previousInit < $this->init)
                $auxDate = $auxDate->sub(new DateInterval("P1D"));
            if ($previousInit > $auxDate)
            {
                $auxOtherExtraHours = $this->netExtraHours($auxDate, $previousInit, $user);
                $auxOtherExtraHours[$user->getLogin()]["extra_hours"] =
                        (-1) * $auxOtherExtraHours[$user->getLogin()]["extra_hours"];
            } else
                if ($previousInit != $auxDate)
                    $auxOtherExtraHours = $this->netExtraHours($previousInit, $auxDate, $user);
                else
                    $auxOtherExtraHours = 0;

            $otherExtraHours[$user->getLogin()] = $auxOtherExtraHours[$user->getLogin()];

            if (!is_null($previous))
            {
                if ($previous->getDate() >= $this->init)
                {
                    $work[$user->getLogin()]["extra_hours"] +=
                            $otherExtraHours[$user->getLogin()]["extra_hours"]
                            + $previousExtraHours[$user->getLogin()];
                    $totalExtraHours[$user->getLogin()] =
                            $work[$user->getLogin()]["extra_hours"];
                }
                else
                    $totalExtraHours[$user->getLogin()] =
                            $work[$user->getLogin()]["extra_hours"]
                            + $otherExtraHours[$user->getLogin()]["extra_hours"]
                            + $previousExtraHours[$user->getLogin()];
            } else
                $totalExtraHours[$user->getLogin()] =
                        $work[$user->getLogin()]["extra_hours"]
                        + $otherExtraHours[$user->getLogin()]["extra_hours"]
                        + $previousExtraHours[$user->getLogin()];

            $addResults["total_hours"] += $work[$user->getLogin()]["total_hours"];
            $addResults["workable_hours"] += $work[$user->getLogin()]["workable_hours"];
            $addResults["extra_hours"] += $work[$user->getLogin()]["extra_hours"];
            $addResults["total_extra_hours"] += $totalExtraHours[$user->getLogin()];
            $work[$user->getLogin()]["total_extra_hours"] = $totalExtraHours[$user->getLogin()];

            $allWork[$user->getLogin()] = $work[$user->getLogin()];

        }

        $results = array($addResults, $allWork);

        return $results;
    }

}

/*//Uncomment these lines in order to do a simple test of the Action

$dao = DAOFactory::getUserDAO();

//$init = "2000-01-01";
//$end = "2009-11-11";
//$user = $dao->getByUserLogin("jaragunde");

if (is_null($user))
{
    $groupDAO = DAOFactory::getUserGroupDAO();
    $users = $groupDAO->getUsersByUserGroupName(
            ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
}
else
    $users[] = $user;

$init = date_create($init);
$end = date_create($end);
$action= new ExtraHoursReportAction($init, $end, $user);

$newResults = $action->execute();
$newUsersResults = $newResults[1];

foreach($users as $k)
{
    print "\nUser: " . $k->getLogin() . "\n";
    print "Worked: " . $newUsersResults[$k->getLogin()]["total_hours"] . "\n";
    print "Workable hours: " . $newUsersResults[$k->getLogin()]["workable_hours"] . "\n";
    print "Extra hours: " . $newUsersResults[$k->getLogin()]["extra_hours"] . "\n";
    print "Total extra hours: " . $newUsersResults[$k->getLogin()]["total_extra_hours"] . "\n";

}
*/
