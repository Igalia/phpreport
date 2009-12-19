<?php

/** File for UpdateCityHistoryAction
 *
 *  This file just contains {@link UpdateCityHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CityHistoryVO.php');

/** Update City History entry Action
 *
 *  This action is used for updating an entry on City History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateCityHistoryAction extends Action{

    /** The City History
     *
     * This variable contains the City History entry we want to update.
     *
     * @var CityHistoryVO
     */
    private $cityHistory;

    /** UpdateCityHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CityHistoryVO $cityHistory the City History value object we want to update.
     */
    public function __construct(CityHistoryVO $cityHistory) {
        $this->cityHistory=$cityHistory;
        $this->preActionParameter="UPDATE_CITY_HISTORY_PREACTION";
        $this->postActionParameter="UPDATE_CITY_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the City History entry on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCityHistoryDAO();
        if ($dao->update($this->cityHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$cityhistoryvo= new CityHistoryVO();
$cityhistoryvo->setCityId(1);
$cityhistoryvo->setId(1);
$cityhistoryvo->setUserId(1);
$cityhistoryvo->setInitDate(date_create("2009-01-01"));
$cityhistoryvo->setEndDate(date_create("2009-06-01"));
$action= new UpdateHourCostHistoryAction($cityhistoryvo);
var_dump($action);
$action->execute();
var_dump($cityhistoryvo);
*/
