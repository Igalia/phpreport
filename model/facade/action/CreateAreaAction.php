<?php

/** File for CreateAreaAction
 *
 *  This file just contains {@link CreateAreaAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/AreaVO.php');

/** Create Area Action
 *
 *  This action is used for creating a new Area.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateAreaAction extends Action{

    /** The Area
     *
     * This variable contains the Area we want to create.
     *
     * @var AreaVO
     */
    private $area;

    /** CreateAreaAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param AreaVO $area the Area value object we want to create.
     */
    public function __construct(AreaVO $area) {
        $this->area=$area;
        $this->preActionParameter="CREATE_AREA_PREACTION";
        $this->postActionParameter="CREATE_AREA_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Area, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getAreaDAO();
        if ($dao->create($this->area)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$areavo = new AreaVO();
$areavo->setName('Deliverers');
$action= new CreateAreaAction($areavo);
var_dump($action);
$action->execute();
var_dump($areavo);
*/
