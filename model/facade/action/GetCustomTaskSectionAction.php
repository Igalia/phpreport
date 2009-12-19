<?php

/** File for GetCustomTaskSectionAction
 *
 *  This file just contains {@link GetSectionTaskSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/GetSectionCustomTaskSectionsAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskSectionVO.php');
include_once('phpreport/model/vo/CustomTaskSectionVO.php');


/** Get Custom Task Section Action
 *
 *  This action is used for retrieving a custom Task Section (Task Section with additional data) by it's id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomTaskSectionAction extends GetSectionCustomTaskSectionsAction {

    /** The Task Section Id
     *
     * This variable contains the id of the Custom Task Section we want to retieve.
     *
     * @var int
     */
    private $taskSectionId;

    /** GetCustomTaskSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $taskSectionId the id of the Custom Task Section we want to retieve.
     */
    public function __construct($taskSectionId) {
        $this->taskSectionId=$taskSectionId;
        $this->preActionParameter="GET_CUSTOM_TASK_SECTION_PREACTION";
        $this->postActionParameter="GET_CUSTOM_TASK_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Section from persistent storing and calls the function
     * that creates the Custom Object.
     *
     * @return CustomTaskSectionVO a custom object {@link CustomTaskSectionVO} with its properties set to the values from the rows
     * and with additional data.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskSectionDAO();

    $taskSections[] = $dao->getById($this->taskSectionId);

    if ($taskSections[0] == NULL)
        return NULL;

    $customTaskSections = $this->TaskSectionsToCustomTaskSections($taskSections);

    return $customTaskSections[0];

    }

}


/*//Test code;

$action= new GetCustomTaskSectionAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
