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


/** File for BaseTaskSectionVO
 *
 *  This file just contains {@link BaseTaskSectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Base Task Sections
 *
 *  This class just stores Base Task Section data, which will be extended by
 *  specific classes.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Section.
 *  @property double $estHours estimated number of hours this Task Section will last.
 *  @property int $sectionId database internal identifier of the associated Section.
 */
abstract class BaseTaskSectionVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $risk = NULL;
    protected $name = NULL;
    protected $estHours = NULL;
    protected $sectionId = NULL;

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

    public function setRisk($risk) {
    if (is_null($risk))
        $this->risk = $risk;
    else
            $this->risk = (int) $risk;
    }

    public function getRisk() {
        return $this->risk;
    }

    public function setEstHours($estHours) {
    if (is_null($estHours))
        $this->estHours = $estHours;
    else
            $this->estHours = (double) $estHours;
    }

    public function getEstHours() {
        return $this->estHours;
    }

    public function setSectionId($sectionId) {
        if (is_null($sectionId))
        $this->sectionId = $sectionId;
    else
            $this->sectionId = (int) $sectionId;
    }

    public function getSectionId() {
        return $this->sectionId;
    }

    /**#@-*/

}
