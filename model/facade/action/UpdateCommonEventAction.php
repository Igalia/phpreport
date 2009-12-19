<?php

/** File for UpdateCommonEventAction
 *
 *  This file just contains {@link UpdateCommonEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CommonEventVO.php');

/** Update Common Event Action
 *
 *  This action is used for updating a Common Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateCommonEventAction extends Action{

    /** The Common Event
     *
     * This variable contains the Common Event we want to update.
     *
     * @var CommonEventVO
     */
    private $commonEvent;

    /** UpdateCommonEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CommonEventVO $commonEvent the Common Event value object we want to update.
     */
    public function __construct(CommonEventVO $commonEvent) {
        $this->commonEvent=$commonEvent;
        $this->preActionParameter="UPDATE_COMMON_EVENT_PREACTION";
        $this->postActionParameter="UPDATE_COMMON_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Common Event on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCommonEventDAO();
        if ($dao->update($this->commonEvent)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$commoneventvo = new commonEventVO();

$commoneventvo->setDate(date_create('2007-12-21'));
$commoneventvo->setCityId(1);
$commoneventvo->setId(1);

$action= new UpdateCommonEventAction($commoneventvo);
var_dump($action);
$action->execute();
var_dump($commoneventvo);
*/
