<?php

/** File for UpdateHourCostHistoryAction
 *
 *  This file just contains {@link UpdateHourCostHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/HourCostHistoryVO.php');

/** Update Hour Cost History entry Action
 *
 *  This action is used for updating an entry on Hour Cost History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateHourCostHistoryAction extends Action{

    /** The Hour Cost History
     *
     * This variable contains the Hour Cost History entry we want to update.
     *
     * @var HourCostHistoryVO
     */
    private $hourCostHistory;

    /** UpdateHourCostHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param HourCostHistoryVO $hourCostHistory the Hour Cost History value object we want to update.
     */
    public function __construct(HourCostHistoryVO $hourCostHistory) {
        $this->hourCostHistory=$hourCostHistory;
        $this->preActionParameter="UPDATE_HOUR_COST_HISTORY_PREACTION";
        $this->postActionParameter="UPDATE_HOUR_COST_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Hour Cost History entry on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getHourCostHistoryDAO();
        if ($dao->update($this->hourCostHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$hourcosthistoryvo= new HourCostHistoryVO();
$hourcosthistoryvo->setHourCost(5.5);
$hourcosthistoryvo->setId(1);
$hourcosthistoryvo->setUserId(1);
$hourcosthistoryvo->setInitDate(date_create("2009-01-01"));
$hourcosthistoryvo->setEndDate(date_create("2009-06-01"));
$action= new UpdateHourCostHistoryAction($hourcosthistoryvo);
var_dump($action);
$action->execute();
var_dump($hourcosthistoryvo);
*/
