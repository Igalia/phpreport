<?php

require_once("phpreport/test/FacadeTests/ExtraHoursReportActionTest.php");

class ExtraHoursReportActionLargeTest extends ExtraHoursReportActionTest
{

        public function testExtraHourReport1Year()
        {

        $this->loopTest("P1Y");

        }

    public function testExtraHourReport6Months()
        {

        $this->loopTest("P6M");

        }

}
?>
