<?php

/** File for JourneyHistoryVO
 *
 *  This file just contains {@link JourneyHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/BaseHistoryVO.php');

/** VO for Journey Histories
 *
 *  This class just stores Journey History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 *  @property double $journey journey of the User.
 */
class JourneyHistoryVO extends BaseHistoryVO
{

    /**#@+
     *  @ignore
     */
    protected $journey = NULL;

    public function setJourney($journey) {
        if (is_null($journey))
        $this->journey = $journey;
    else
            $this->journey = (double) $journey;
    }

    public function getJourney() {
        return $this->journey;
    }

    /**#@-*/

}
