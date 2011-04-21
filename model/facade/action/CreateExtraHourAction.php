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


/** File for CreateExtraHourAction
 *
 *  This file just contains {@link CreateExtraHourAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ExtraHourVO.php');

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
