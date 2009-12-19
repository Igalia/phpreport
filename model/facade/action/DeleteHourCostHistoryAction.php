<?php

/** File for DeleteHourCostHistoryAction
 *
 *  This file just contains {@link DeleteHourCostHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/HourCostHistoryVO.php');

/** Delete Hour Cost History entry Action
 *
 *  This action is used for deleting an entry in Hour Cost History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteHourCostHistoryAction extends Action{

    /** The Hour Cost History
     *
     * This variable contains the Hour Cost History entry we want to delete.
     *
     * @var HourCostHistoryVO
     */
    private $hourCostHistory;

    /** DeleteHourCostHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param HourCostHistoryVO $hourCostHistory the Hour Cost History value object we want to delete.
     */
    public function __construct(HourCostHistoryVO $hourCostHistory) {
        $this->hourCostHistory=$hourCostHistory;
        $this->preActionParameter="DELETE_HOUR_COST_HISTORY_PREACTION";
        $this->postActionParameter="DELETE_HOUR_COST_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Hour Cost History entry from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getHourCostHistoryDAO();
        if ($dao->delete($this->hourCostHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$hourcosthistoryvo= new HourCostHistoryVO();
$hourcosthistoryvo->setId(1);
$action= new DeleteHourCostHistoryAction($hourcosthistoryvo);
var_dump($action);
$action->execute();
var_dump($hourcosthistoryvo);
*/
