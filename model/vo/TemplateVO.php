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


/** File for TemplateVO
 *
 *  This file just contains {@link TemplateVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Tony Thomas <tthomas@igalia.com>
 */

/** VO for Templates
 *
 *  This class just stores Templates data.
 *
 *  @property int $id database internal identifier.
 *  @property string $name name of the template
 *  @property string $story story of this Task.
 *  @property boolean $telework says if this Task was made by telework.
 *  @property boolean $onsite says if this Task was made onsite.
 *  @property string $text text describing this Task.
 *  @property string $ttype type of this Task.
 *  @property int $userId database internal identifier of the associated User.
 *  @property int $projectId database internal identifier of the associated Project.
 *  @property int $customerId database internal identifier of the associated Customer.
 *  @property int $taskStoryId database internal identifier of the associated Task Story.
 */
class TemplateVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $name = NULL;
    protected $story= NULL;
    protected $telework = NULL;
    protected $onsite = NULL;
    protected $text = NULL;
    protected $ttype = NULL;
    protected $userId = NULL;
    protected $projectId = NULL;
    protected $taskStoryId = NULL;

    public function setId($id) {
        if (is_null($id))
            $this->id = $id;
        else
            $this->id = (int) $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( $name ) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStory() {
        return $this->story;
    }

    /**
     * @param string $story
     */
    public function setStory( $story ) {
        $this->story = $story;
    }

    /**
     * @return boolean
     */
    public function isTelework() {
        return $this->telework;
    }

    /**
     * @param boolean $telework
     */
    public function setTelework($telework) {
        $this->telework = $telework;
    }

    /**
     * @return boolean
     */
    public function isOnsite() {
        return $this->onsite;
    }

    /**
     * @param boolean $onsite
     */
    public function setOnsite($onsite) {
        $this->onsite = $onsite;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTtype() {
        return $this->ttype;
    }

    /**
     * @param string $ttype
     */
    public function setTtype($ttype) {
        $this->ttype = $ttype;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    /**
     * @return int
     */
    public function getCustomerId() {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId($customerId) {
        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getTaskStoryId() {
        return $this->taskStoryId;
    }

    /**
     * @param int $taskStoryId
     */
    public function setTaskStoryId($taskStoryId) {
        $this->taskStoryId = $taskStoryId;
    }

    /** Covert the TemplateVO object to XML string
     *
     * @return string
     */
    public function toXml() {
        $string = "";
        $string .= "<template><id>{$this->id}</id>";
        $string .= "<name>{$this->name}</name>";
        $string .= "<story>{$this->story}</story>";
        $string .= "<telework>{$this->telework}</telework>";
        $string .= "<onsite>{$this->onsite}</onsite>";
        $string .= "<text>{$this->text}</text>";
        $string .= "<ttype>{$this->ttype}</ttype>";
        $string .= "<userId>{$this->userId}</userId>";
        $string .= "<projectId>{$this->projectId}</projectId>";
        $string .= "<customerId>{$this->customerId}</customerId>";
        $string .= "<taskStoryId>{$this->taskStoryId}</taskStoryId>";
        $string .= "</template>";

        return $string;
    }
}
