<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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


if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');
require_once(PHPREPORT_ROOT . "/vendor/autoload.php");
include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

use it\thecsea\simple_caldav_client\SimpleCalDAVClient;

class SyncCalendarAction extends Action
{
    protected $user;
    protected $datesRanges;

    public function __construct(UserVO $user = NULL, array $datesRanges = [])
    {
        $this->user = $user;
        $this->datesRanges = $datesRanges;
        $this->preActionParameter = "SYNC_CALENDAR_PREACTION";
        $this->postActionParameter = "SYNC_CALENDAR_POSTACTION";
    }

    protected function doExecute()
    {
        $url = ConfigurationParametersManager::getParameter('CALENDAR_URL');
        $calendarUser = ConfigurationParametersManager::getParameter('CALENDAR_USERNAME');
        $password = ConfigurationParametersManager::getParameter('CALENDAR_PASSWORD');
        $companyDomain = ConfigurationParametersManager::getParameter('COMPANY_DOMAIN');
        $calendarEmail = ConfigurationParametersManager::getParameter('CALENDAR_EMAIL');

        if (!$url || !$calendarUser || !$password || !$companyDomain)
            throw new Exception("CalDAV calendar not configured correctly");

        $client = new SimpleCalDAVClient();

        $client->connect($url, $calendarUser, $password);
        $arrayOfCalendars = $client->findCalendars();

        $client->setCalendar($arrayOfCalendars[ConfigurationParametersManager::getParameter('CALENDAR_ID')]);

        $lastYear = date("Y") - 1;
        $nextYear = date("Y") + 1;
        $start = gmdate("Ymd\THis\Z", strtotime($lastYear . "-01-01"));
        $end = gmdate("Ymd\THis\Z", strtotime($nextYear . "-12-31"));
        $currentEvents = $client->getEvents($start, $end);
        foreach ($currentEvents as $event) {
            if (strstr($event->getData(), $this->user->getLogin() . "-phpreport")) {
                $client->delete($event->getHref());
            }
        }

        foreach ($this->datesRanges as $range) {
            if (!is_array($range) || !isset($range['start'])) continue;

            $start = str_replace("-", "", $range['start']);
            $end = str_replace("-", "", $range['end']);
            $userEmail = $this->user->getLogin() . "@" . $companyDomain;
            $event = 'BEGIN:VCALENDAR
PRODID:-//Igalia PhPReport//CalDAV Client//EN
VERSION:2.0
BEGIN:VEVENT
UID:' . $start . '-' . $this->user->getLogin() . '-phpreport
SUMMARY:' . $this->user->getLogin() . ' holiday
DESCRIPTION:Event created automatically from PhpReport
CLASS:PUBLIC
X-SOGO-SEND-APPOINTMENT-NOTIFICATIONS:NO
ATTENDEE;PARTSTAT=TENTATIVE;CN=' . $this->user->getLogin() . ';RSVP=TRUE;ROLE=REQ-PARTICIPANT:mailto:' . $userEmail . '
TRANSP:OPAQUE
DTSTART;VALUE=DATE:' . $start . '
DTEND;VALUE=DATE:' . $end . '
ORGANIZER;CN=' . $calendarUser . ':mailto:' . $calendarEmail . '
X-SOGO-COMPONENT-CREATED-BY:' . $userEmail . '
DTSTAMP:' . $start . '
END:VEVENT
END:VCALENDAR';

            $client->create($event);
        }
    }
}
