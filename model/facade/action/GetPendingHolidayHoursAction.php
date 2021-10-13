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

include_once(PHPREPORT_ROOT . '/model/facade/action/GetHolidayHoursBaseAction.php');

/** Get pending Holiday Hours Action
 *
 *  This action is used for retrieving pending holiday hours for Users.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetPendingHolidayHoursAction extends GetHolidayHoursBaseAction
{
    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL)
    {
        parent::__construct($init, $end, $user);
        $this->preActionParameter = "GET_PENDING_HOLIDAY_HOURS_PREACTION";
        $this->postActionParameter = "GET_PENDING_HOLIDAY_HOURS_POSTACTION";
    }

    protected function doExecute()
    {
        return $this->getHoursSummary()['pendingHours'];
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
