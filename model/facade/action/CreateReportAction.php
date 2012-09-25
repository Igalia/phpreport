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


/** File for CreateReportAction
 *
 *  This file just contains {@link CreateReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

/** Create Task Action
 *
 *  This action is used for creating a new Task.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateReportAction extends Action{

    /** The Task
     *
     * This variable contains the Task we want to create.
     *
     * @var TaskVO
     */
    private $task;

    /** CreateReportAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskVO $task the Task value object we want to create.
     */
    public function __construct(TaskVO $task) {
        $this->task=$task;
        $this->preActionParameter="CREATE_REPORT_PREACTION";
        $this->postActionParameter="CREATE_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Task, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {
        //first check if current configuration allows saving tasks in that date
        $configDao = DAOFactory::getConfigDAO();
        if(!$configDao->isWriteAllowedForDate($this->task->getDate())) {
            return -1;
        }

        $dao = DAOFactory::getTaskDAO();
        if ($dao->create($this->task)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$taskvo = new TaskVO();

$taskvo->setDate(date_create('2009-01-05'));
$taskvo->setInit(1);
$taskvo->setEnd(2);
$taskvo->setStory("Very well");
$taskvo->setTelework("FALSE");
$taskvo->setText("Old text");
$taskvo->setTtype("Ttype 1");
$taskvo->setPhase("Initial");
$taskvo->setUserId(1);
$taskvo->setProjectId(1);
$taskvo->setCustomerId(1);

$action= new CreateReportAction($taskvo);
var_dump($action);
$action->execute();
var_dump($taskvo);
*/
