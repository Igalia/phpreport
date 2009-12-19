<?php

/** File for HourCostHistoryVO
 *
 *  This file just contains {@link HourCostHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/BaseHistoryVO.php');

/** VO for Hour Cost Histories
 *
 *  This class just stores Hour Cost History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 *  @property double $hourCost hour cost of the User.
 */
class HourCostHistoryVO extends BaseHistoryVO
{

    /**#@+
     *  @ignore
     */
    protected $hourCost = NULL;

    public function setHourCost($hourCost) {
        if (is_null($id))
        $this->hourCost = $hourCost;
    else
            $this->hourCost = (double) $hourCost;
    }

    public function getHourCost() {
        return $this->hourCost;
    }

    /**#@-*/

}
