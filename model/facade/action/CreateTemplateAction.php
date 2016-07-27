<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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


/** File for CreateTemplateAction
 *
 *  This file just contains {@link CreateTemplateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Tony Thomas <tthomas@igalia.com>
 */
include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');

/** Create Templates Action
 *
 *  This action is used for creating a Template object
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Tony Thomas <tthomas@igalia.com>
 */
class CreateTemplateAction extends Action {

    /** The Template
     *
     * This variable contains an array with the templates objects we want to create.
     *
     * @var array
     */
    private $templates;

    /** CreateTemplateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param array $tasks an array with the templates objects we want to create.
     */
    public function __construct($templates) {
        $this->templates=$templates;
        $this->preActionParameter="CREATE_TEMPLATE_PREACTION";
        $this->postActionParameter="CREATE_TEMPLATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * Runs the action itself.
     *
     * @return int it just indicates if there was any error (<i>-1</i>)
     *         or not (<i>0</i>).
     */
    protected function doExecute() {
        $templateDao = DAOFactory::getTemplateDAO();

        if($templateDao->batchCreate($this->templates) < count($this->templates)) {
            return -1;
        }

        return 0;
    }

}
