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


/** File for GetAllCustomProjectsAction
 *
 *  This file just contains {@link GetAllCustomProjectsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** Get Filtered Custom Projects Action
 *
 *  This action is used for retrieving projects filtered by multiple fields, and
 *  and return them as Custom Projects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class GetFilteredCustomProjectsAction extends Action{

    private $description;
    private $filterStartDate;
    private $filterEndDate;
    private $activation;
    private $areaId;
    private $type;

    /** GetAllCustomProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $description string to filter projects by their description
     *        field. Projects with a description that contains this string will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterStartDate start date of the time filter for
     *        projects. Projects will a finish date later than this date will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterEndDate end date of the time filter for projects.
     *        Projects will a start date sooner than this date will be returned.
     *        NULL to deactivate filtering by this field.
     * @param boolean $activation filter projects by their activation field.
     *        NULL to deactivate filtering by this field.
     * @param long $areaId value to filter projects by their area field.
     *        projects. NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only trojects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     */
    public function __construct($description = NULL, $filterStartDate = NULL,
            $filterEndDate = NULL, $activation = NULL, $areaId = NULL,
            $type = NULL) {

        $this->preActionParameter="GET_FILTERED_CUSTOM_PROJECTS_PREACTION";
        $this->postActionParameter="GET_FILTERED_CUSTOM_PROJECTS_POSTACTION";

        $this->description = $description;
        $this->filterStartDate = $filterStartDate;
        $this->filterEndDate = $filterEndDate;
        $this->activation = $activation;
        $this->areaId = $areaId;
        $this->type = $type;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns all CustomProjects.
     *
     * @return array an array with all the existing CustomProjects.
     */
    protected function doExecute() {
        $dao = DAOFactory::getProjectDAO();
        return $dao->getFilteredCustom($this->description,
            $this->filterStartDate, $this->filterEndDate, $this->activation,
            $this->areaId, $this->type);
    }

}
