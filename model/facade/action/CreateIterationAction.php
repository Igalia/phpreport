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


/** File for CreateIterationAction
 *
 *  This file just contains {@link CreateIterationAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');

/** Create Iteration Action
 *
 *  This action is used for creating a new Iteration.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateIterationAction extends Action{

    /** The Iteration
     *
     * This variable contains the Iteration we want to create.
     *
     * @var IterationVO
     */
    private $project;

    /** CreateIterationAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param IterationVO $iteration the Iteration value object we want to create.
     */
    public function __construct(IterationVO $iteration) {
        $this->iteration=$iteration;
        $this->preActionParameter="CREATE_ITERATION_PREACTION";
        $this->postActionParameter="CREATE_ITERATION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Iteration, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getIterationDAO();
        if ($dao->create($this->iteration)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$iterationvo = new IterationVO;

$iterationvo->setName("Qwark delivery");
$iterationvo->setInit(date_create('2009-06-01'));
$iterationvo->setEnd(date_create('2009-06-08'));
$iterationvo->setSummary("Bad news boys!");
$iterationvo->setProjectId(1);

$action= new CreateIterationAction($iterationvo);
var_dump($action);
$action->execute();
var_dump($iterationvo);
*/
