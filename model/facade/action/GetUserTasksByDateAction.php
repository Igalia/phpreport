<?php

/** File for GetUserTasksByDateAction
 *
 *  This file just contains {@link GetUserTasksByDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Get Tasks by User Id and Date Action
 *
 *  This action is used for retrieving tasks of a User on a date.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserTasksByDateAction extends Action{

    /** The User
     *
     * This variable contains the User whose tasks we want to retrieve.
     *
     * @var UserVO
     */
    private $userVO;

    /** The date
     *
     * This variable contains the date whose tasks we want to retrieve.
     *
     * @var DateTime
     */
    private $date;

    /** CreateUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User whose tasks we want to retrieve.
     * @param DateTime $date the date whose tasks we want to retrieve.
     */
    public function __construct(UserVO $userVO, DateTime $date) {
        $this->userVO = $userVO;
    $this->date = $date;
        $this->preActionParameter="CREATE_USER_PREACTION";
        $this->postActionParameter="CREATE_USER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Tasks.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskDAO();
        return $dao->getByUserIdDate($this->userVO->getId(), $this->date);

    }

}


/*//Test code

$uservo= new UserVO();
$uservo->setLogin('jjsantos');
$uservo->setPassword('jaragunde');
$action= new CreateUserAction($uservo);
var_dump($action);
$action->execute();
var_dump($uservo);
*/
