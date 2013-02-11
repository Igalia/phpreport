<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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


/** File for DirtyTaskVO
 *
 *  This file just contains {@link DirtyTaskVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

/** Custom VO for dirty tasks
 *
 * This class contains the data of a TaskVO entity and a flag per property to
 * indicate whether that property is "dirty" (has been updated) or not.
 *
 */
class DirtyTaskVO extends TaskVO {

    private $dirtyDate = false;
    private $dirtyInit = false;
    private $dirtyEnd = false;
    private $dirtyStory = false;
    private $dirtyTelework = false;
    private $dirtyOnsite = false;
    private $dirtyText = false;
    private $dirtyTtype = false;
    private $dirtyPhase = false;
    private $dirtyUserId = false;
    private $dirtyProjectId = false;
    private $dirtyCustomerId = false;
    private $dirtyTaskStoryId = false;

    public function setDate(DateTime $_date = NULL) {
        $this->dirtyDate = true;
        parent::setDate($_date);
    }

    public function clearDirtyDate() {
        $this->dirtyDate = false;
    }

    public function isDateDirty() {
        return $this->dirtyDate;
    }

    public function setInit($init) {
        $this->dirtyInit = true;
        parent::setInit($init);
    }

    public function clearDirtyInit() {
        $this->dirtyInit = false;
    }

    public function isInitDirty() {
        return $this->dirtyInit;
    }

    public function setEnd($_end) {
        $this->dirtyEnd = true;
        parent::setEnd($_end);
    }

    public function clearDirtyEnd() {
        $this->dirtyEnd = false;
    }

    public function isEndDirty() {
        return $this->dirtyEnd;
    }

    public function setStory($story) {
        $this->dirtyStory = true;
        parent::setStory($story);
    }

    public function clearDirtyStory() {
        $this->dirtyStory = false;
    }

    public function isStoryDirty() {
        return $this->dirtyStory;
    }

    public function setTelework($telework) {
        $this->dirtyTelework = true;
        parent::setTelework($telework);
    }

    public function clearDirtyTelework() {
        $this->dirtyTelework = false;
    }

    public function isTeleworkDirty() {
        return $this->dirtyTelework;
    }

    public function setOnsite($onsite) {
        $this->dirtyOnsite = true;
        parent::setOnsite($onsite);
    }

    public function clearDirtyOnsite() {
        $this->dirtyOnsite = false;
    }

    public function isOnsiteDirty() {
        return $this->dirtyOnsite;
    }

    public function setText($text) {
        $this->dirtyText = true;
        parent::setText($text);
    }

    public function clearDirtyText() {
        $this->dirtyText = false;
    }

    public function isTextDirty() {
        return $this->dirtyText;
    }

    public function setTtype($ttype) {
        $this->dirtyTtype = true;
        parent::setTtype($ttype);
    }

    public function clearDirtyTtype() {
        $this->dirtyTtype = false;
    }

    public function isTtypeDirty() {
        return $this->dirtyTtype;
    }

    public function setPhase($phase) {
        $this->dirtyPhase = true;
        parent::setPhase($phase);
    }

    public function clearDirtyPhase() {
        $this->dirtyPhase = false;
    }

    public function isPhaseDirty() {
        return $this->dirtyPhase;
    }

    public function setUserId($userId) {
        $this->dirtyUserId = true;
        parent::setUserId($userId);
    }

    public function clearDirtyUserId() {
        $this->dirtyUserId = false;
    }

    public function isUserIdDirty() {
        return $this->dirtyUserId;
    }

    public function setProjectId($projectId) {
        $this->dirtyProjectId = true;
        parent::setProjectId($projectId);
    }

    public function clearDirtyProjectId() {
        $this->dirtyProjectId = false;
    }

    public function isProjectIdDirty() {
        return $this->dirtyProjectId;
    }

    public function setCustomerId($customerId) {
        $this->dirtyCustomerId = true;
        parent::setCustomerId($customerId);
    }

    public function clearDirtyCustomerId() {
        $this->dirtyCustomerId = false;
    }

    public function isCustomerIdDirty() {
        return $this->dirtyCustomerId;
    }

    public function setTaskStoryId($taskStoryId) {
        $this->dirtyTaskStoryId = true;
        parent::setTaskStoryId($taskStoryId);
    }

    public function clearDirtyTaskStoryId() {
        $this->dirtyTaskStoryId = false;
    }

    public function isTaskStoryIdDirty() {
        return $this->dirtyTaskStoryId;
    }

    public function isDirty() {
        return $this->dirtyDate || $this->dirtyInit || $this->dirtyEnd ||
                $this->dirtyStory || $this->dirtyTelework ||
                $this->dirtyOnsite ||
                $this->dirtyText || $this->dirtyTtype || $this->dirtyPhase ||
                $this->dirtyUserId || $this->dirtyProjectId ||
                $this->dirtyCustomerId || $this->dirtyTaskStoryId;
    }
}
