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


/** File for OperationResult
 *
 *  This file just contains {@link OperationResult}.
 *
 * @filesource
 * @package PhpReport
 * @author Danielle Mayabb <danielle@igalia.com>
 */

/** Result returned after CRUD operations
 *
 *  This is the object returned for all CRUD operations
 *
  *  @property boolean $isSuccessful Whether the operation was successful or not
  *  @property int $responseCode The http status code 
  *  @property int $errorNumber The php error number
  *  @property string $message Error message that is passed up to front end. Can be php error or custom string.
 */
class OperationResult {
	public $isSuccessful;
	public $responseCode;
	public $errorNumber;
	public $message;

	function __construct($isSuccessful, $responseCode = null) {
		$this->isSuccessful = $isSuccessful;
		$this->responseCode = $responseCode;
	}

	public function getIsSuccessful(){
		return $this->isSuccessful;
	}

	public function setIsSuccessful($isSuccessful){
		$this->isSuccessful = (boolean) $isSuccessful;
	}

	public function getResponseCode(){
		return $this->responseCode;
	}

	public function setResponseCode($responseCode){
		$this->responseCode = (int) $responseCode;
	}

	public function getErrorNumber(){
		return $this->errorNumber;
	}

	public function setErrorNumber($errorNumber){
		$this->errorNumber = (int) $errorNumber;
	}

	public function getMessage(){
		return $this->message;
	}

	public function setMessage($message){
		$this->message = (string) $message;
	}
}
