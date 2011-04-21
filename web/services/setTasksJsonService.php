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

/** setTasks JSON web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

    $request = trim(file_get_contents('php://input'));

     foreach ($_POST as $key->$post)
         echo ($key . " -> " . $post);

     var_dump($request);

    /*$request = '{"tasks": [{"id": "124291", "date": "2009-06-01", "initTime": "420", "endTime": "450", "story": "ItEr86S02Unexpected", "text": "Solving a problem with the Kindergarten application.", "userId": "114", "projectId": "138", "customerId": "10"}]}';

    $decoded = json_decode($request, TRUE);

    foreach ($decoded["tasks"] as $decodedTask)
    {
    $task = new TaskVO();

    $task->setDate(date_create($decodedTask["date"]));
    $task->setInit($decodedTask["initTime"]);
    $task->setEnd($decodedTask["endTime"]);
    $task->setInit($decodedTask["initTime"]);
    $task->setStory($decodedTask["story"]);
    $task->setTelework($decodedTask["telework"]);
    $task->setTtype($decodedTask["ttype"]);
    $task->setText($decodedTask["text"]);
    $task->setUserId($decodedTask["userId"]);
    $task->setProjectId($decodedTask["projectId"]);
    $task->setCustomerId($decodedTask["customerId"]);

    if (is_null($decodedTask["id"]))
        $createTasks[] = $task;
    else
    {
        $task->setId($decodedTask["id"]);
        $updateTasks[] = $task;
    }

    }

    if (count($createTasks) >= 1)
    foreach ($createTasks as $task)
            TasksFacade::CreateReport($task);



    if (count($updateTasks) >= 1)
    foreach ($updateTasks as $task)
        TasksFacade::UpdateReport($task);*/
