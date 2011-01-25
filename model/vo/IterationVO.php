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


/** File for IterationVO
 *
 *  This file just contains {@link IterationVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Iterations
 *
 *  This class just stores Iteration data.
 *
 *  @property int $id database internal identifier.
 *  @property DateTime $init date this Iteration began at.
 *  @property DateTime $end date this Iteration ended at.
 *  @property string $summary a summary of this Iteration.
 *  @property string $name name of this Iteration.
 *  @property int $projectId database internal identifier of the associated Project.
 */
class IterationVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $name = NULL;
    protected $init = NULL;
    protected $_end = NULL;
    protected $summary = NULL;
    protected $projectId = NULL;

    public function setId($id) {
    if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setInit(DateTime $init = NULL) {
        $this->init = $init;
    }

    public function getInit() {
        return $this->init;
    }

    public function setEnd(DateTime $end = NULL) {
        $this->_end = $end;
    }

    public function getEnd() {
        return $this->_end;
    }

    public function setSummary($summary) {
        $this->summary = (string) $summary;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function setProjectId($projectId) {
        if (is_null($projectId))
        $this->projectId = $projectId;
    else
            $this->projectId = (int) $projectId;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    /**#@-*/

}
