<?php

/** File for CustomEventVO
 *
 *  This file just contains {@link CustomEventVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Custom Events
 *
 *  This class just stores Custom Event data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $date date of the Custom Event.
 *  @property double $hours hours the Custom Event took from working ones.
 *  @property string $type type of the Custom Event.
 */
class CustomEventVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $userId = NULL;
    protected $date = NULL;
    protected $hours = NULL;
    protected $type = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
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

    public function setDate(DateTime $date = NULL) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    public function setHours($hours) {
        if (is_null($hours))
        $this->hours = $hours;
    else
            $this->hours = (double) $hours;
    }

    public function getHours() {
        return $this->hours;
    }

    public function setType($type) {
        $this->type = (string) $type;
    }

    public function getType() {
        return $this->type;
    }

    /**#@-*/

}
