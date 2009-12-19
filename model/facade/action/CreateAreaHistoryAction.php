<?php

/** File for CreateAreaHistoryAction
 *
 *  This file just contains {@link CreateAreaHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/AreaHistoryVO.php');

/** Create Area History entry Action
 *
 *  This action is used for creating a new entry on Area History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateAreaHistoryAction extends Action{

    /** The Area History
     *
     * This variable contains the Area History entry we want to create.
     *
     * @var AreaHistoryVO
     */
    private $areaHistory;

    /** CreateAreaHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param AreaHistoryVO $areaHistory the Area History value object we want to create.
     */
    public function __construct(AreaHistoryVO $areaHistory) {
        $this->areaHistory=$areaHistory;
        $this->preActionParameter="CREATE_AREA_HISTORY_PREACTION";
        $this->postActionParameter="CREATE_AREA_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Area History entry, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getAreaHistoryDAO();
        if ($dao->create($this->areaHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$areahistoryvo= new AreaHistoryVO();
$areahistoryvo->setAreaId(1);
$areahistoryvo->setUserId(1);
$areahistoryvo->setInitDate(date_create("2009-01-01"));
$areahistoryvo->setEndDate(date_create("2009-06-01"));
$action= new CreateAreaHistoryAction($areahistoryvo);
var_dump($action);
$action->execute();
var_dump($areahistoryvo);
*/
