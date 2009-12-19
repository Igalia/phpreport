<?php

/** File for DeleteAreaAction
 *
 *  This file just contains {@link DeleteAreaAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/AreaVO.php');

/** Delete Area Action
 *
 *  This action is used for deleting an Area.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteAreaAction extends Action{

    /** The Area
     *
     * This variable contains the Area we want to delete.
     *
     * @var AreaVO
     */
    private $area;

    /** DeleteAreaAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param AreaVO $area the Area value object we want to delete.
     */
    public function __construct(AreaVO $area) {
        $this->area=$area;
        $this->preActionParameter="DELETE_AREA_PREACTION";
        $this->postActionParameter="DELETE_AREA_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Area from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getAreaDAO();
        if ($dao->delete($this->area)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$areavo= new AreaVO();
$areavo->setId(1);
$action= new DeleteAreaAction($areavo);
var_dump($action);
$action->execute();
var_dump($areavo);
*/
