<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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


include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

class GetProjectByDescriptionAction extends Action
{

    public function __construct(string $description)
    {
        $this->description = $description;
        $this->preActionParameter = "GET_PROJECT_BY_DESCRIPTION_PREACTION";
        $this->postActionParameter = "GET_PROJECTS_BY_DESCRIPTION_POSTACTION";
    }

    protected function doExecute()
    {
        $dao = DAOFactory::getProjectDAO();
        return $dao->getByDescription($this->description);
    }
}
