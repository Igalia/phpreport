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

/** createAreaHistories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 */

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');

$parser = new XMLReader();

$request = trim(file_get_contents('php://input'));

/*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><areaHistories><areaHistory><userId>81</userId><areaId>1</areaId><init format="Y-m-d">2009-06-01</init><end format="Y-m-d">2009-12-01</end></areaHistory><areaHistory><userId>81</userId><areaId>2</areaId><init format="Y-m-d">2009-01-01</init><end format="Y-m-d">2009-06-01</end></areaHistory></areaHistories>';*/

$parser->XML($request);

do {

	$parser->read();

	if ($parser->name == 'userGoals')
	{

		$sid = $parser->getAttribute("sid");

		$parser->read();

	}

	/* We check authentication and authorization */
	require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

	$user = LoginManager::isLogged($sid);

	if (!$user)
	{
		$string = "<return service='createUserGoals'><error id='2'>You must be logged in</error></return>";
		break;
	}

	if (!LoginManager::isAllowed($sid))
	{
		$string = "<return service='createUserGoals'><error id='3'>Forbidden service for this User</error></return>";
		break;
	}
	$createUserGoals = array();

	do {

		//print ($parser->name . "\n");

		if ($parser->name == "userGoal")
		{

			$userGoalVO = new UserGoalVO();

			$parser->read();

			while ($parser->name != "userGoal") {

				//print ($parser->name . "\n");

				switch ($parser->name ) {

					case "extraTime":$parser->read();
						if ($parser->hasValue)
						{
							$userGoalVO->setExtraHours($parser->value);
							$parser->next();
							$parser->next();
						}
						break;

					case "init":    $dateFormat = $parser->getAttribute("format");
						if (is_null($dateFormat))
							$dateFormat = "Y-m-d";
						$parser->read();
						if ($parser->hasValue)
						{
							$date = $parser->value;
							$dateParse = date_parse_from_format($dateFormat, $date);
							$date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
							$userGoalVO->setInitDate(date_create($date));
							$parser->next();
							$parser->next();
						}
						break;

					case "end":    $dateFormat = $parser->getAttribute("format");
						if (is_null($dateFormat))
							$dateFormat = "Y-m-d";
						$parser->read();
						if ($parser->hasValue)
						{
							$date = $parser->value;
							$dateParse = date_parse_from_format($dateFormat, $date);
							$date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
							$userGoalVO->setEndDate(date_create($date));
							$parser->next();
							$parser->next();
						}
						break;

					case "userId":$parser->read();
						if ($parser->hasValue)
						{
							$userGoalVO->setUserId($parser->value);
							$parser->next();
							$parser->next();
						}
						break;

					default:    $parser->next();
						break;

				}

			}

			$createUserGoals[] = $userGoalVO;

		}

	} while ($parser->read());

	//var_dump($createUsers);


	if (count($createUserGoals) >= 1)
		foreach((array)$createUserGoals as $createUserGoal)
		{
			if (UsersFacade::CreateUserGoal($createUserGoal) == -1)
			{
				$string = "<return service='createUserGoal'><error id='1'>There was some error while creating the area history entries</error></return>";
				break;
			}
		}



	if (!$string)
	{

		$string = "<return service='createUserGoal'><ok>Operation Success!</ok><userGoals>";

		foreach((array) $createUserGoals as $createUserGoal)
		{
			$string = $string . "<userGoal><id>{$createUserGoal->getId()}</id><extraTime>{$createUserGoal->getExtraHours()}</extraTime><init format='Y-m-d'>{$createUserGoal->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$createUserGoal->getEndDate()->format('Y-m-d')}</end></userGoal>";
		}

		$string = $string . "</userGoals></return>";

	}

} while (false);


// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
