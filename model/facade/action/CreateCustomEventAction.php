<?php

/** File for CreateCustomEventAction
 *
 *  This file just contains {@link CreateCustomEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomEventVO.php');

/** Create Custom Event Action
 *
 *  This action is used for creating a new Custom Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCustomEventAction extends Action{

    /** The Custom Event
     *
     * This variable contains the Custom Event we want to create.
     *
     * @var CustomEventVO
     */
    private $customEvent;

    /** CreateCustomEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to create.
     */
    public function __construct(CustomEventVO $customEvent) {
        $this->customEvent=$customEvent;
        $this->preActionParameter="CREATE_CUSTOM_EVENT_PREACTION";
        $this->postActionParameter="CREATE_CUSTOM_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Custom Event, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomEventDAO();

        if ($dao->create($this->customEvent)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$customeventvo = new customEventVO();

$customeventvo->setDate(date_create('2005-12-21'));
$customeventvo->setUserId(1);
$customeventvo->setHours(3);
$customeventvo->setType("Ctrl+Alt+Supr");

$action= new CreateCustomEventAction($customeventvo);
var_dump($action);
$action->execute();
var_dump($customeventvo);
*/
