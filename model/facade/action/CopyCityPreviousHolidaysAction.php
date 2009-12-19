<?php

/** File for CopyCityPreviousHolidaysAction
 *
 *  This file just contains {@link CopyCityPreviousHolidaysAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CityVO.php');

/** Copy previous year holidays for a city Action
 *
 *  This action is used for copying previous year holidays for a city.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CopyCityPreviousHolidaysAction extends Action{

    /** The year we want to copy from.
     *
     * This variable contains the year we want to copy holidays from to the next one.
     *
     * @var int
     */
    private $year;

    /** The City id.
     *
     * This variable contains the identifier of the city whose holidays we want to copy.
     *
     * @var int
     */
    private $cityVO;

    /** CopyCityPreviousHolidaysAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $cityId the City identifier.
     * @param int $year the year we want to copy the holidays from.
     */
    public function __construct(CityVO $cityVO, $year) {
        $this->cityVO=$cityVO;
        $this->year=$year;
        $this->preActionParameter="COPY_PREVIOUS_HOLIDAYS_PREACTION";
        $this->postActionParameter="COPY_PREVIOUS_HOLIDAYS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that copies the holidays for a city from one year to the next one.
     *
     * @return int the number of holiday entries copied.
     */
    protected function doExecute() {

    $dao = DAOFactory::getCommonEventDAO();

    $init = date_create($this->year . "-01-01");
    $end = date_create($this->year . "-12-01");

    $holidays = $dao->getByCityIdDates($this->cityVO->getId(), $init, $end);

    foreach($holidays as $holiday)
    {
        $date = $holiday->getDate();

        $date = $date->add(new DateInterval("P1Y"));

        $holiday->setDate($date);

        $dao->create($holiday);
    }

        return $holidays;
    }

}


/*//Test code

$action= new CopyCityPreviousHolidaysAction(1, 2009);
var_dump($action);
$holidays = $action->execute();
var_dump($holidays);
*/
