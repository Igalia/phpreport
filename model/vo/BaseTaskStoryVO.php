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


/** File for BaseTaskStoryVO
 *
 *  This file just contains {@link BaseTaskStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Base VO for Task Stories
 *
 *  This class just stores Base Task Story data, which will be extended by
 *  specific classes.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Story.
 *  @property DateTime $init date this Task began at.
 *  @property DateTime $end date this Task ended at.
 *  @property double $estHours estimated number of hours this Task Story will last.
 *  @property double $toDo pending working hours in this Task Story.
 *  @property DateTime $estEnd estimated end date of this Task Story.
 *  @property int $storyId database internal identifier of the associated Story.
 *  @property int $taskSectionId database internal identifier of the associated TaskSection.
 */
abstract class BaseTaskStoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $risk = NULL;
    protected $name = NULL;
    protected $estHours = NULL;
    protected $estEnd = NULL;
    protected $toDo = NULL;
    protected $init = NULL;
    protected $end = NULL;
    protected $storyId = NULL;

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

    public function setToDo($toDo) {
    if (is_null($toDo))
        $this->toDo = $toDo;
    else
            $this->toDo = (double) $toDo;
    }

    public function getToDo() {
        return $this->toDo;
    }

    public function setEstEnd(DateTime $estEnd = NULL) {
        $this->estEnd = $estEnd;
    }

    public function getEstEnd() {
        return $this->estEnd;
    }

    public function setInit(DateTime $init = NULL) {
        $this->init = $init;
    }

    public function getInit() {
        return $this->init;
    }

    public function setEnd(DateTime $end = NULL) {
        $this->end = $end;
    }

    public function getEnd() {
        return $this->end;
    }

    public function setStoryId($storyId) {
        if (is_null($storyId))
        $this->storyId = $storyId;
    else
            $this->storyId = (int) $storyId;
    }

    public function getStoryId() {
        return $this->storyId;
    }

    /**#@-*/

}
