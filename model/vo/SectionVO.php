<?php

/** File for SectionVO
 *
 *  This file just contains {@link SectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Sections
 *
 *  This class just stores Section data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Module.
 *  @property string $text text with information about this Section.
 *  @property int $moduleId database internal identifier of the associated Module.
 *  @property int $userId database internal identifier of the associated User (the one who leads the Section).
 */
class SectionVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $accepted = NULL;
    protected $name = NULL;
    protected $text = NULL;
    protected $userId = NULL;
    protected $moduleId = NULL;

    public function setId($id) {
    if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setAccepted($accepted) {
        $this->accepted = (boolean) $accepted;
    }

    public function getAccepted() {
        return $this->accepted;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setText($text) {
        $this->text = (string) $text;
    }

    public function getText() {
        return $this->text;
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

    public function setModuleId($moduleId) {
        if (is_null($moduleId))
        $this->moduleId = $moduleId;
    else
            $this->moduleId = (int) $moduleId;
    }

    public function getModuleId() {
        return $this->moduleId;
    }

    /**#@-*/

}
