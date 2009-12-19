<?php

/** File for DeleteSectorAction
 *
 *  This file just contains {@link DeleteSectorAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/SectorVO.php');

/** Delete Sector Action
 *
 *  This action is used for deleting a Sector.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteSectorAction extends Action{

    /** The Sector
     *
     * This variable contains the Sector we want to delete.
     *
     * @var SectorVO
     */
    private $sector;

    /** DeleteSectorAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param SectorVO $sector the Sector value object we want to delete.
     */
    public function __construct(SectorVO $sector) {
        $this->sector=$sector;
        $this->preActionParameter="DELETE_SECTOR_PREACTION";
        $this->postActionParameter="DELETE_SECTOR_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Sector from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getSectorDAO();
        if ($dao->delete($this->sector)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$sectorvo= new SectorVO();
$sectorvo->setId(1);
$action= new DeleteSectorAction($sectorvo);
var_dump($action);
$action->execute();
var_dump($sectorvo);
*/
