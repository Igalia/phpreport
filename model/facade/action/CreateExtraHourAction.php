<?php

/** File for CreateExtraHourAction
 *
 *  This file just contains {@link CreateExtraHourAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ExtraHourVO.php');

/** Create Extra Hour Action
 *
 *  This action is used for creating a new Extra Hour.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateExtraHourAction extends Action{

    /** The Extra Hour
     *
     * This variable contains the Extra Hour we want to create.
     *
     * @var ExtraHourVO
     */
    private $extraHour;

    /** CreateExtraHourAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ExtraHourVO $extraHour the Extra Hour value object we want to create.
     */
    public function __construct(ExtraHourVO $extraHour) {
        $this->extraHour=$extraHour;
        $this->preActionParameter="CREATE_EXTRA_HOUR_PREACTION";
        $this->postActionParameter="CREATE_EXTRA_HOUR_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Extra Hour, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getExtraHourDAO();
        if ($dao->create($this->extraHour)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$extrahourvo= new ExtraHourVO();
$extrahourvo->setUserId(1);
$extrahourvo->setDate(date_create("2009-06-01"));
$extrahourvo->setHours(3);
$action= new CreateExtraHourAction($extrahourvo);
var_dump($action);
$action->execute();
var_dump($extrahourvo);
*/
