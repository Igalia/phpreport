<?php

/** File for UpdateAreaAction
 *
 *  This file just contains {@link UpdateAreaAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Update Area Action
 *
 *  This action is used for updating an Area.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateSectorAction extends Action{

    /** The Area
     *
     * This variable contains the Area we want to update.
     *
     * @var AreaVO
     */
    private $area;

    /** UpdateAreaAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param AreaVO $area the Area value object we want to update.
     */
    public function __construct(AreaVO $area) {
        $this->area=$area;
        $this->preActionParameter="UPDATE_AREA_PREACTION";
        $this->postActionParameter="UPDATE_AREA_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Area on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getAreaDAO();
        if ($dao->update($this->area)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$areavo= new AreaVO();
$areavo->setId(1);
$areavo->setName('Pizza Deliverers');
$action= new UpdateAreaAction($areavo);
var_dump($action);
$action->execute();
var_dump($areavo);
*/
