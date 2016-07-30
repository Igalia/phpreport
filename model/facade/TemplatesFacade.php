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


/** File for ProjectsFacade
 *
 *  This file just contains {@link ProjectsFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Tony Thomas <tthomas@igalia.com>
 */
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateTemplateAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteTemplateAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserTemplatesAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');

/** Templates Facade
 *
 *  This Facade contains the functions used in tasks related to Templates.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Tony Thomas <tthomas@igalia.com>
 */
abstract class TemplatesFacade {

    /** Create Templates function
     *
     * @param $templates
     * @return mixed
     * @throws null
     */
    static function CreateTemplates($templates) {
        $action = new CreateTemplateAction($templates);

        return $action->execute();
    }

    /** Delete Templates function
     *
     * @param $templates
     * @throws null
     */
    static function DeleteTemplates($templates) {
        $action = new DeleteTemplateAction($templates);

        $action->execute();
    }

    /** Fetch Templates for a user function
     *
     * @param $userId
     * @return mixed
     * @throws null
     */
    static function GetUserTemplates($userId) {
        $action = new GetUserTemplatesAction($userId);

        return $action->execute();
    }
}
