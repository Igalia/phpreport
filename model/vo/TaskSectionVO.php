<?php

/** File for TaskSectionVO
 *
 *  This file just contains {@link TaskSectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Task Sections
 *
 *  This class just stores Task Section data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Section.
 *  @property double $estHours estimated number of hours this Task Section will last.
 *  @property int $sectionId database internal identifier of the associated Section.
 *  @property int $userId database internal identifier of the associated User.
 */
class TaskSectionVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $risk = NULL;
    protected $name = NULL;
    protected $estHours = NULL;
    protected $sectionId = NULL;
    protected $userId = NULL;

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

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    /**#@-*/

}
