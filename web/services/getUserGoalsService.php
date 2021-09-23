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

/** getUserGoals web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 */

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');

$userLogin = $_GET['uid'] ?? NULL;

$sid = $_GET['sid'] ?? NULL;

do {
	/* We check authentication and authorization */
	require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

	if (!LoginManager::isLogged($sid))
	{
		$string = "<report";
		if ($userLogin!="")
			$string = $string . " login='" . $userLogin . "'";
		$string = $string . "><error id='2'>You must be logged in</error></report>";
		break;
	}

	if (!LoginManager::isAllowed($sid))
	{
		$string = "<report";
		if ($userLogin!="")
			$string = $string . " login='" . $userLogin . "'";
		$string = $string . "><error id='3'>Forbidden service for this User</error></report>";
		break;
	}

	$userGoals = UsersFacade::GetUserGoals($userLogin);

	$string = "<userGoals";
	if ($userLogin!="")
		$string = $string . " login='" . $userLogin . "'";
	$string = $string . ">";

	foreach((array)$userGoals as $userGoal)
	{
		$string = $string . "<userGoal><id>{$userGoal->getId()}</id><extraTime>{$userGoal->getExtraHours()}</extraTime><init format='Y-m-d'>{$userGoal->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$userGoal->getEndDate()->format('Y-m-d')}</end></userGoal>";
	}

	$string = $string . "</userGoals>";

} while (False);

// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
