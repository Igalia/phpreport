<?php

/** File for DeleteIterationAction
 *
 *  This file just contains {@link DeleteIterationAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/IterationVO.php');

/** Delete Iteration Action
 *
 *  This action is used for deleting an Iteration.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteIterationAction extends Action{

    /** The Iteration
     *
     * This variable contains the Iteration we want to delete.
     *
     * @var IterationVO
     */
    private $iteration;

    /** DeleteIterationAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param IterationVO $iteration the Iteration value object we want to delete.
     */
    public function __construct(SectorVO $iteration) {
        $this->iteration=$iteration;
        $this->preActionParameter="DELETE_ITERATION_PREACTION";
        $this->postActionParameter="DELETE_ITERATION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Iteration from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getIterationDAO();
        if ($dao->delete($this->iteration)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$iterationvo= new IterationVO();
$iterationvo->setId(1);
$action= new DeleteIterationAction($iterationvo);
var_dump($action);
$action->execute();
var_dump($iterationvo);
*/
