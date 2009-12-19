<?php

/** File for BaseHistoryVO
 *
 *  This file just contains {@link BaseHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Base VO for Histories
 *
 *  This class is extended by every History VO we create, and it just stores History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 */
abstract class BaseHistoryVO
{
    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $userId = NULL;
    protected $initDate = NULL;
    protected $endDate = NULL;

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

    public function setInitDate(DateTime $initDate = NULL) {
        $this->initDate = $initDate;
    }

    public function getInitDate() {
        return $this->initDate;
    }

    public function setEndDate(DateTime $endDate = NULL) {
        $this->endDate = $endDate;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    /**#@-*/

}
