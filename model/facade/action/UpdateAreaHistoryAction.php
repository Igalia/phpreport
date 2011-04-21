<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for UpdateAreaHistoryAction
 *
 *  This file just contains {@link UpdateAreaHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaHistoryVO.php');

/** Update Area History entry Action
 *
 *  This action is used for updating an entry on Area History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateAreaHistoryAction extends Action{

    /** The Area History
     *
     * This variable contains the Area History entry we want to update.
     *
     * @var AreaHistoryVO
     */
    private $areaHistory;

    /** UpdateAreaHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param AreaHistoryVO $areaHistory the Area History value object we want to update.
     */
    public function __construct(AreaHistoryVO $areaHistory) {
        $this->areaHistory=$areaHistory;
        $this->preActionParameter="UPDATE_AREA_HISTORY_PREACTION";
        $this->postActionParameter="UPDATE_AREA_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Area History entry on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getAreaHistoryDAO();
        if ($dao->update($this->areaHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$areahistoryvo= new AreaHistoryVO();
$areahistoryvo->setAreaId(1);
$areahistoryvo->setId(1);
$areahistoryvo->setUserId(1);
$areahistoryvo->setInitDate(date_create("2009-01-01"));
$areahistoryvo->setEndDate(date_create("2009-06-01"));
$action= new UpdateAreaHistoryAction($areahistoryvo);
var_dump($action);
$action->execute();
var_dump($areahistoryvo);
*/
