<?php

/** File for DeleteCityHistoryAction
 *
 *  This file just contains {@link DeleteCityHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CityHistoryVO.php');

/** Delete City History entry Action
 *
 *  This action is used for deleting an entry in City History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteCityHistoryAction extends Action{

    /** The City History
     *
     * This variable contains the City History entry we want to delete.
     *
     * @var CityHistoryVO
     */
    private $cityHistory;

    /** DeleteCityHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CityHistoryVO $cityHistory the City History value object we want to delete.
     */
    public function __construct(CityHistoryVO $cityHistory) {
        $this->cityHistory=$cityHistory;
        $this->preActionParameter="DELETE_CITY_HISTORY_PREACTION";
        $this->postActionParameter="DELETE_CITY_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the City History entry from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getCityHistoryDAO();
        if ($dao->delete($this->cityHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$cityhistoryvo= new CityHistoryVO();
$cityhistoryvo->setId(1);
$action= new DeleteCityHistoryAction($cityhistoryvo);
var_dump($action);
$action->execute();
var_dump($cityhistoryvo);
*/
