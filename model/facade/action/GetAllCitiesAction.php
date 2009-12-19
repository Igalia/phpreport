<?php

/** File for GetAllCitiesAction
 *
 *  This file just contains {@link GetAllCitiesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get all Cities Action
 *
 *  This action is used for retrieving all Cities.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllCitiesAction extends Action{

    /** GetAllCitiesAction constructor.
     *
     * This is just the constructor of this action.
     */
    public function __construct() {
        $this->preActionParameter="GET_ALL_CITIES_PREACTION";
        $this->postActionParameter="GET_ALL_CITIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Cities from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getCityDAO();

        return $dao->getAll();

    }

}


//Test code;

/*$action= new GetAllCitiesAction();
var_dump($action);
$result = $action->execute();
var_dump($result);
 */
