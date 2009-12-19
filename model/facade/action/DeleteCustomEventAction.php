<?php

/** File for DeleteCustomEventAction
 *
 *  This file just contains {@link DeleteCustomEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomEventVO.php');

/** Delete Custom Event Action
 *
 *  This action is used for deleting a Custom Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteCustomEventAction extends Action{

    /** The Custom Event
     *
     * This variable contains the Custom Event we want to delete.
     *
     * @var CustomEventVO
     */
    private $customEvent;

    /** DeleteCustomEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomEventVO $CustomEvent the Custom Event value object we want to delete.
     */
    public function __construct(CustomEventVO $customEvent) {
        $this->customEvent=$customEvent;
        $this->preActionParameter="DELETE_CUSTOM_EVENT_PREACTION";
        $this->postActionParameter="DELETE_CUSTOM_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Custom Event from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomEventDAO();

        if ($dao->delete($this->customEvent)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$customeventvo = new customEventVO();

$customeventvo->setId(1);

$action= new DeleteCustomEventAction($customeventvo);
var_dump($action);
$action->execute();
var_dump($customeventvo);
*/
