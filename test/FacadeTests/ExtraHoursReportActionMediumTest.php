<?php

require_once("phpreport/test/FacadeTests/ExtraHoursReportActionTest.php");

class ExtraHoursReportActionMediumTest extends ExtraHoursReportActionTest
{

    public function testExtraHourReport3Months()
        {

        $this->loopTest("P3M");

        }

    public function testExtraHourReport1Month()
        {

        $this->loopTest("P1M");

        }

}
?>
