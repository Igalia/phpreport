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


/** File for GetUserTemplatesAction
 *
 *  This file just contains {@link GetTemplatesAction}.
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

/** Create GetUserTemplatesAction Action
 *
 *  This action is used for fetching  a Template object
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Tony Thomas <tthomas@igalia.com>
 */
class GetUserTemplatesAction extends Action {

    /** The User
     *
     * @var int
     */
    private $userId;

    /** GetUserTemplatesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId The user id, for whom we are fetching the data
     */
    public function __construct($userId) {
        $this->userId=$userId;
        $this->preActionParameter="GET_USER_TEMPLATE_PREACTION";
        $this->postActionParameter="GET_USER_TEMPLATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * Runs the action itself.
     *
     * @return array $templates The templates array, fetched from the db
     */
    protected function doExecute() {
        $templateDao = DAOFactory::getTemplateDAO();

        return $templateDao->getUserTemplates($this->userId);
    }

}