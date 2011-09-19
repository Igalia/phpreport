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


/** File for CustomProjectVO
 *
 *  This file just contains {@link CustomProjectVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

/** VO for Custom Projects
 *
 *  This class just stores Project detailed and derived data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $activation says if the Project is active or not.
 *  @property double $invoice invoice of this Project.
 *  @property double $estHours number of working hours we have estimated this Project will require.
 *  @property DateTime $init beginning date of this Project.
 *  @property DateTime $_end end date (included) of this Project.
 *  @property int $areaId database internal identifier of the associated Area.
 *  @property string $description description of this Project.
 *  @property double $movedHours hours this Project has moved from the estimation.
 *  @property double $workedHours hours of work spent on this Project.
 *  @property double $totalCost total cost of the hours spent by employees on this Project.
 *  @property string $type type of this Project.
 *  @property string $schedType type of scheduling this Project has.
 */
class CustomProjectVO extends ProjectVO {

    /**#@+
     *  @ignore
     */
    protected $workedHours = NULL;
    protected $totalCost = NULL;

    public function setTotalCost($totalCost) {
        if (is_null($totalCost))
            $this->totalCost = $totalCost;
        else
            $this->totalCost = (double) $totalCost;
    }

    public function getTotalCost() {
        return $this->totalCost;
    }

    public function setWorkedHours($workedHours) {
        if (is_null($workedHours))
            $this->workedHours = $workedHours;
        else
            $this->workedHours = (double) $workedHours;
    }

    public function getWorkedHours() {
        return $this->workedHours;
    }

    /**#@-*/

    /** Get final estimated hours (estimated hours - moved hours).
     *
     * @return {double} final estimated hours.
     */
    public function getFinalEstHours() {
        return ($this->estHours-$this->movedHours);
    }

    /** Get Estimated Work Deviation Percentage
     *
     *  This function returns the deviation percentage of the estimated work hours.
     *
     * @return double the deviation percentage of the estimated work hours according to the worked ones.
     */
    public function getPercDev() {
        if ($this->estHours > 0)
            return (($this->workedHours/$this->getFinalEstHours())-1)*100;
        else return null;
    }

    /** Get Estimated Work Deviation
     *
     *  This function returns the deviation of the estimated work hours.
     *
     * @return double the deviation of the estimated work hours according to the worked ones.
     */
    public function getAbsDev() {
        return ($this->workedHours-$this->getFinalEstHours());
    }

    /** Get Estimated Hours Invoice
     *
     *  This function returns the hour invoice according to the estimated working hours.
     *
     * @return double the estimated hour invoice.
     */
    public function getEstHourInvoice() {
        if ($this->estHours > 0)
            return ($this->invoice/$this->getFinalEstHours());
        else return null;
    }

    /** Get Total Profit
     *
     *  This function simply returns the total profit of the Project according to the current work hours.
     *
     * @return double the total profit of the Project.
     */
    public function getTotalProfit() {
        return ($this->invoice-$this->totalCost);
    }

    /** Get Hour Profit
     *
     *  This function returns the profit (total profit) per worked hour.
     *
     * @return double the profit per worked hour.
     */
    public function getHourProfit() {
        if ($this->workedHours > 0)
            return ($this->getTotalProfit()/$this->workedHours);
        else return null;
    }

    /** Get Hour Invoice
     *
     *  This function returns the invoice per worked hour.
     *
     * @return double the invoice per worked hour.
     */
    public function getWorkedHourInvoice() {
        if ($this->workedHours > 0)
            return ($this->invoice/$this->workedHours);
        else return null;
    }

    /** Get Absolute Hour Invoice Deviation
     *
     *  This function returns the absolute deviation between the estimated
     *  invoice per hour and the actual invoice per hour taking into account the
     *  actual time devoted, measuring it in money.
     *
     * @return {double} the deviation on invoice per hour measured in money.
     */
    public function getWorkedHourInvoiceAbsoluteDeviation() {
        if ($this->workedHours > 0)
            return ($this->getEstHourInvoice() - $this->getWorkedHourInvoice());
        else return null;
    }

    /** Get Relative Hour Invoice Deviation
     *
     *  This function returns the relative deviation between the estimated
     *  invoice per hour and the actual invoice per hour taking into account the
     *  actual time devoted, measuring it in percentage.
     *
     * @return {double} the deviation on invoice per hour measured in percentage.
     */
    public function getWorkedHourInvoiceRelativeDeviation() {
        if ($this->workedHours > 0)
            return (100*$this->getWorkedHourInvoiceAbsoluteDeviation()/
                    $this->getEstHourInvoice());
        else return null;
    }

}
