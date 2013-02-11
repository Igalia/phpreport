<?php
/*
 * Copyright (C) 2009-2013 Igalia, S.L. <info@igalia.com>
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


/** File for TaskVO
 *
 *  This file just contains {@link TaskVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/DirtyTaskVO.php');

/** VO for Tasks
 *
 *  This class just stores Task data.
 *
 *  @property int $id database internal identifier.
 *  @property DateTime $_date date of this task.
 *  @property int $init time this Task began at.
 *  @property int $_end time this Task ended at.
 *  @property string $story story of this Task.
 *  @property boolean $telework says if this Task was made by telework.
 *  @property boolean $onsite says if this Task was made onsite.
 *  @property string $text text describing this Task.
 *  @property string $ttype type of this Task.
 *  @property string $phase phase of this Task.
 *  @property int $userId database internal identifier of the associated User.
 *  @property int $projectId database internal identifier of the associated Project.
 *  @property int $customerId database internal identifier of the associated Customer.
 *  @property int $taskStoryId database internal identifier of the associated Task Story.
 */
class TaskVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $_date = NULL;
    protected $init = NULL;
    protected $_end = NULL;
    protected $story = NULL;
    protected $telework = NULL;
    protected $onsite = NULL;
    protected $text = NULL;
    protected $ttype = NULL;
    protected $phase = NULL;
    protected $userId = NULL;
    protected $projectId = NULL;
    protected $customerId = NULL;
    protected $taskStoryId = NULL;

    public function setId($id) {
    if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setDate(DateTime $_date = NULL) {
        $this->_date = $_date;
    }

    public function getDate() {
        return $this->_date;
    }

    public function setInit($init) {
    if (is_null($init))
        $this->init = $init;
    else
            $this->init = (int) $init;
    }

    public function getInit() {
        return $this->init;
    }

    public function setEnd($_end) {
    if (is_null($_end))
        $this->_end = $_end;
    else
            $this->_end = (int) $_end;
    }

    public function getEnd() {
        return $this->_end;
    }

    public function setStory($story) {
        $this->story = (string) $story;
    }

    public function getStory() {
        return $this->story;
    }

    public function setTelework($telework) {
        $this->telework = (boolean) $telework;
    }

    public function setOnsite($onsite) {
        $this->onsite = (boolean) $onsite;
    }

    public function getTelework() {
        return $this->telework;
    }

    public function getOnsite() {
        return $this->onsite;
    }

    public function setText($text) {
        $this->text = (string) $text;
    }

    public function getText() {
        return $this->text;
    }

    public function setTtype($ttype) {
        $this->ttype = (string) $ttype;
    }

    public function getTtype() {
        return $this->ttype;
    }

    public function setPhase($phase) {
        $this->phase = (string) $phase;
    }

    public function getPhase() {
        return $this->phase;
    }

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
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

    public function setCustomerId($customerId) {
        if (is_null($customerId))
        $this->customerId = $customerId;
    else
            $this->customerId = (int) $customerId;
    }

    public function getCustomerId() {
        return $this->customerId;
    }

    public function setTaskStoryId($taskStoryId) {
        if (is_null($taskStoryId))
        $this->taskStoryId = $taskStoryId;
    else
            $this->taskStoryId = (int) $taskStoryId;
    }

    public function getTaskStoryId() {
        return $this->taskStoryId;
    }

    /**#@-*/

    /**
     * Checks if the entity is new or if it has been already stored in DB.
     * @return {boolean} true if it has been stored in DB and, in consequence,
     *         it has been assigned an id.
     */
    public function isNew() {
        return is_null($this->id);
    }

    /**
     * Updates the fields of this object with the changes in a
     * {@link DirtyTaskVO} object.
     */
    public function updateFrom(DirtyTaskVO $dirtyTask) {
        if ($dirtyTask->isDateDirty())
            $this->setDate($dirtyTask->getDate());

        if ($dirtyTask->isInitDirty())
            $this->setInit($dirtyTask->getInit());

        if ($dirtyTask->isEndDirty())
            $this->setEnd($dirtyTask->getEnd());

        if ($dirtyTask->isStoryDirty())
            $this->setStory($dirtyTask->getStory());

        if ($dirtyTask->isTeleworkDirty())
            $this->setTelework($dirtyTask->getTelework());

        if ($dirtyTask->isTextDirty())
            $this->setText($dirtyTask->getText());

        if ($dirtyTask->isTtypeDirty())
            $this->setTtype($dirtyTask->getTtype());

        if ($dirtyTask->isPhaseDirty())
            $this->setPhase($dirtyTask->getPhase());

        if ($dirtyTask->isUserIdDirty())
            $this->setUserId($dirtyTask->getUserId());

        if ($dirtyTask->isProjectIdDirty())
            $this->setProjectId($dirtyTask->getProjectId());

        if ($dirtyTask->isCustomerIdDirty())
            $this->setCustomerId($dirtyTask->getCustomerId());

        if ($dirtyTask->isTaskStoryIdDirty())
            $this->setTaskStoryId($dirtyTask->getTaskStoryId());
    }

}
