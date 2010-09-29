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


include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/facade/actionplugin/ActionPlugin.php');

class BeforeSendDeletedTaskToNavalPlan extends ActionPlugin {

    public static $taskToBeDeleted;

    public function __construct($action) {
        $this->pluggedAction = $action;
    }

    public function run($status) {
        if ($this->pluggedAction instanceof DeleteReportAction) {
            // the task is going to be deleted, but we need its data
            // later to send them to NavalPlan; we retrieve the data
            // now and store them here for later
            $dao = DAOFactory::getTaskDAO();
            BeforeSendDeletedTaskToNavalPlan::$taskToBeDeleted =
                $dao->getById($this->pluggedAction->getTaskVO()->getId());
        }
        // if the action doesn't belong to one of those classes,
        // we do nothing
    }
}
