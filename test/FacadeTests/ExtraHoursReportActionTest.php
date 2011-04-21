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


require_once(PHPREPORT_ROOT . "/model/facade/action/ExtraHoursReportAction.php");
require_once(PHPREPORT_ROOT . "/util/oldExtraHours.php");

abstract class ExtraHoursReportActionTest extends PHPUnit_Framework_TestCase
{
    protected $init = "2005-01-01";

    protected $end = NULL ;

    protected function loopTest($addLoop)
    {

        $this->init = date_create($this->init);

        if (is_null($this->end))
            $this->end = new DateTime();
        else
            $this->end = date_create($this->end);

        while ($this->init <= $this->end)
        {
            $init = clone ($this->init);
            $end = $init->add(new DateInterval($addLoop));
            $this->init = date_create($this->init->format("Y-m-d"));
            $end = $end->sub(new DateInterval("P1D"));
            if ($end > new DateTime())
                $end = new DateTime();
            $end = date_create($end->format("Y-m-d"));

            $oldUsersResults = oldExtraHours($this->init->format("Y-m-d"), $end->format("Y-m-d"));

            $users = getValidUsers($this->init->format("Y-m-d"), $end->format("Y-m-d"));

            $action= new ExtraHoursReportAction($this->init, $end);

            $newResults = $action->execute();
            $newUsersResults = $newResults[1];

            foreach($users as $k)
            {
                /*if (is_null($oldUsersResults[$k]["workable_hours"]))
                    $oldUsersResults[$k]["workable_hours"] = 0;
                $this->assertEquals(number_format($oldUsersResults[$k]["workable_hours"], 5), number_format($newUsersResults[$k]["workable_hours"], 5));

                if (is_null($oldUsersResults[$k]["extra_hours"]))
                    $oldUsersResults[$k]["extra_hours"] = 0;
                $this->assertEquals(number_format($oldUsersResults[$k]["extra_hours"], 5), number_format($newUsersResults[$k]["extra_hours"], 5));*/

                if (is_null($oldUsersResults[$k]["total_hours"]))
                    $oldUsersResults[$k]["total_hours"] = 0;
                $this->assertEquals(number_format($oldUsersResults[$k]["total_hours"], 5), number_format($newUsersResults[$k]["total_hours"], 5));

                if (is_null($oldUsersResults[$k]["total_extra_hours"]))
                    $oldUsersResults[$k]["total_extra_hours"] = 0;
                $this->assertEquals(number_format($oldUsersResults[$k]["total_extra_hours"], 5), number_format($newUsersResults[$k]["total_extra_hours"], 5));

            }

            $this->init = $this->init->add(new DateInterval($addLoop));
            $this->init = date_create($this->init->format("Y-m-d"));
        }

    }

}
?>
