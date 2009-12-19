<?php

/** File for TaskReportInvalidParameterException
 *
 *  This file just contains {@link TaskReportInvalidParameterException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for TaskReport incorrect types on {@link TaskDAO}
 *
 *  This is the exception thrown when an invalid value is passed to optional fields of methods {@link TaskDAO::getTaskReport()} or {@link TaskDAO::getGlobalTaskReport()}.
 *  <br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

class TaskReportInvalidParameterException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "The value " . $message . " is not valid for function TaskReport";
  }

    /**#@-*/

}
