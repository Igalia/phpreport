<?php

/** File for CreateSectorAction
 *
 *  This file just contains {@link CreateSectorAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/SectorVO.php');

/** Create Sector Action
 *
 *  This action is used for creating a new Sector.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateSectorAction extends Action{

    /** The Sector
     *
     * This variable contains the Sector we want to create.
     *
     * @var SectorVO
     */
    private $sector;

    /** CreateSectorAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param SectorVO $sector the Sector value object we want to create.
     */
    public function __construct(SectorVO $sector) {
        $this->sector=$sector;
        $this->preActionParameter="CREATE_SECTOR_PREACTION";
        $this->postActionParameter="CREATE_SECTOR_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Sector, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectorDAO();
        if ($dao->create($this->sector)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$sectorvo = new SectorVO();
$sectorvo->setName('Pizza Delivery');
$action= new CreateSectorAction($sectorvo);
var_dump($action);
$action->execute();
var_dump($sectorvo);
*/
