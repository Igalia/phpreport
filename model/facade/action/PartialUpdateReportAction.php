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


/** File for PartialUpdateReportAction
 *
 *  This file just contains {@link PartialUpdateReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

/** Partial Update Report Action
 *
 *  This action is used for updating only some fields of a Task.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class PartialUpdateReportAction extends Action{

    /** The Task
     *
     * This variable contains the Task we want to update and the information
     * about which fields must be updated.
     *
     * @var DirtyTaskVO
     */
    private $task;

    /** PartialUpdateReportAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param DirtyTaskVO $task the Task value object we want to update. Must be
     *        a DirtyTaskVO object which contains also the information about
     *        which fields must be updated.
     */
    public function __construct(DirtyTaskVO $task) {
        $this->task=$task;
        $this->preActionParameter="PARTIAL_UPDATE_REPORT_PREACTION";
        $this->postActionParameter="PARTIAL_UPDATE_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Task on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {
        //first check if current configuration allows saving tasks in that date
        $configDao = DAOFactory::getConfigDAO();
        if(!$configDao->isWriteAllowedForDate($this->task->getDate())) {
            return -1;
        }


        $dao = DAOFactory::getTaskDAO();

        if (!$dao->checkTaskUserId($this->task->getId(), $this->task->getUserId()))
            return -1;

        if ($dao->partialUpdate($this->task)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code
$taskvo = new TaskVO();

$taskvo->setId(1);

$taskvo->setDate(date_create('2010-04-23'));
$taskvo->setInit(1);
$taskvo->setEnd(2);
$taskvo->setStory("Very bad :'(");
$taskvo->setTelework("FALSE");
$taskvo->setText("Old text");
$taskvo->setTtype("Ttype 1");
$taskvo->setPhase("Initial");
$taskvo->setUserId(65);
$taskvo->setProjectId(1);

$update[story] = true;

$action= new PartialUpdateReportAction($taskvo, $update);
var_dump($action);
$action->execute();
var_dump($taskvo);*/
