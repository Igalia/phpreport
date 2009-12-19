<?php

require_once("phpreport/test/FacadeTests/ExtraHoursReportActionTest.php");

class ExtraHoursReportActionVerySmallTest extends ExtraHoursReportActionTest
{

    public function testExtraHourReport1Week()
        {

        $this->loopTest("P7D");

        }

}
?>
