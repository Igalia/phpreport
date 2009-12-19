<?php

/** File for GetIterationAction
 *
 *  This file just contains {@link GetIterationAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Iteration Action
 *
 *  This action is used for retrieving an Iteration.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetIterationAction extends Action{

    /** The Iteration id
     *
     * This variable contains the id of the Iteration we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetIterationAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Iteration we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_ITERATION_PREACTION";
        $this->postActionParameter="GET_ITERATION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Iteration from persistent storing.
     *
     * @return IterationVO the Iteration as a {@link IterationVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getIterationDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetIterationAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
