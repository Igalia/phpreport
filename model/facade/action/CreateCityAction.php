<?php

/** File for CreateCityAction
 *
 *  This file just contains {@link CreateCityAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CityVO.php');

/** Create City Action
 *
 *  This action is used for creating a new City.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCityAction extends Action{

    /** The City
     *
     * This variable contains the City we want to create.
     *
     * @var CityVO
     */
    private $area;

    /** CreateCityAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CityVO $city the City value object we want to create.
     */
    public function __construct(CityVO $city) {
        $this->city=$city;
        $this->preActionParameter="CREATE_CITY_PREACTION";
        $this->postActionParameter="CREATE_CITY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new City, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCityDAO();
        if ($dao->create($this->city)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$cityvo = new CityVO();
$cityvo->setName('New New York');
$action= new CreateCityAction($cityvo);
var_dump($action);
$action->execute();
var_dump($cityvo);
*/
