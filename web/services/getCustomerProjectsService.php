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

/** getCustomerProjects web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');

    $customerId = $_GET['cid'];

    $active = $_GET['active'];

    if (strtolower($active) == "true")
        $active = True;
    else
        $active = False;

    $login = $_GET['login'];

    $sid = $_GET['sid'];

    $onlyUser = $_GET['onlyUser'];

    $order = $_GET['order'];
    if ($order == '')
        $order = 'id';

    if (strtolower($onlyUser) == "true")
        $onlyUser = True;
    else
        $onlyUser = False;


    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<projects";
            if ($customerId!="")
            $string = $string . " cid='" . $customerId . "'";
            if ($active)
            $string = $string . " active = 'True'";
            if ($onlyUser)
            $string = $string . " onlyUser = 'True' order='" . $order . "'";
            $string = $string . "><error id='2'>You must be logged in</error></projects>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<projects";
            if ($customerId!="")
            $string = $string . " cid='" . $customerId . "'";
            if ($active)
            $string = $string . " active = 'True'";
            if ($onlyUser)
            $string = $string . " onlyUser = 'True' order ='" . $order . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></projects>";
            break;
            }

        if ($customerId == "")
        {
          $projects = ProjectsFacade::GetProjectsByCustomerUserLogin(NULL, NULL, $active, $order);
            $string = "<projects";
        if ($active)
            $string = $string . " active = 'True' order='" . $order . "'>";
        else
            $string = $string . " order='" . $order . "'>";
        }
        else
        {
            if ($onlyUser)
            {
              $projects = ProjectsFacade::GetProjectsByCustomerUserLogin($customerId, $login, $active, $order);
                $string = "<projects cid='" . $customerId . "' login='" . $login . "'";

            } else
            {
              $projects = ProjectsFacade::GetProjectsByCustomerUserLogin($customerId, NULL, $active, $order);
                $string = "<projects cid='" . $customerId . "'";
            }

            if ($active)
            $string = $string . " active = 'True' order='" . $order . "'>";
            else
            $string = $string . " order='" . $order . "'>";


        }

        foreach((array) $projects as $project)
        {

        $string = $string . "<project><id>{$project->getId()}</id><areaId>{$project->getAreaId()}</areaId><activation>{$project->getActivation()}</activation><description>" . escape_string($project->getDescription()) . "</description><invoice>{$project->getInvoice()}</invoice>";

        if (!is_null($project->getInit()))
            $string = $string . "<initDate format='Y-m-d'>{$project->getInit()->format("Y-m-d")}</initDate>";
        else    $string = $string . "<initDate/>";

        if (!is_null($project->getEnd()))
            $string = $string . "<endDate format='Y-m-d'>{$project->getEnd()->format("Y-m-d")}</endDate>";
        else    $string = $string . "<endDate/>";

        $string = $string . "<estHours>{$project->getEstHours()}</estHours><type>" . escape_string($project->getType()) . "</type><movedHours>{$project->getMovedHours()}</movedHours><schedType>" . escape_string($project->getSchedType()) . "</schedType></project>";

        }

        $string = $string . "</projects>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
