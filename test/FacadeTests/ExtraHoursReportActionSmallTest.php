<?php

require_once("phpreport/test/FacadeTests/ExtraHoursReportActionTest.php");

class ExtraHoursReportActionSmallTest extends ExtraHoursReportActionTest
{

    public function testExtraHourReport15Days()
        {

        $this->loopTest("P15D");

        }

}
?>
