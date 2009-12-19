<?php

require_once("include/util.php");
require_once("phpreport/model/facade/action/ExtraHoursReportAction.php");
require_once("phpreport/util/oldExtraHours.php");

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
