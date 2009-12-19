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


/** File for CustomTaskStoryVO
 *
 *  This file just contains {@link CustomTaskStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Custom VO for Task Stories
 *
 *  This class just stores Task Story and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Story.
 *  @property DateTime $init date this Iteration began at.
 *  @property DateTime $end date this Iteration ended at.
 *  @property DateTime $estEnd estimated end date of this Task Story.
 *  @property double $estHours estimated number of hours this Task Story will last.
 *  @property double $spent working hours spent in this Task Story.
 *  @property double $toDo pending working hours in this Task Story.
 *  @property int $storyId database internal identifier of the associated Story.
 *  @property TaskSectionVO $taskSection Task Section that contains this working.
 *  @property UserVO $developer developer of this Task Story.
 *  @property UserVO $reviewer reviewer of this Task Story.
 */
class CustomTaskStoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $risk = NULL;
    protected $name = NULL;
    protected $estHours = NULL;
    protected $spent = NULL;
    protected $toDo = NULL;
    protected $estEnd = NULL;
    protected $init = NULL;
    protected $end = NULL;
    protected $taskSection = NULL;
    protected $storyId = NULL;
    protected $developer = NULL;
    protected $reviewer = NULL;

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

    public function setSpent($spent) {
    if (is_null($spent))
        $this->spent = $spent;
    else
            $this->spent = (double) $spent;
    }

    public function getSpent() {
        return $this->spent;
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

    public function setTaskSection(TaskSectionVO $taskSection) {
        $this->taskSection = $taskSection;
    }

    public function getTaskSection() {
        return $this->taskSection;
    }

    public function setDeveloper(UserVO $developer) {
        $this->developer = $developer;
    }

    public function getDeveloper() {
        return $this->developer;
    }

    public function setReviewer(UserVO $reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    /**#@-*/

}
