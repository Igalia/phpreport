<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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

/** This file contains some simple tests for PostgreSQLConfigDAO,
 *  not PhpUnit-ized.
 *
 * @filesource
 * @package PhpReport
 * @subpackage test
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

define('PHPREPORT_ROOT', __DIR__ . '/../../');

include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

$dao = DAOFactory::getConfigDAO();
var_dump($dao->getVersionNumber());
var_dump($dao->setTaskBlockConfiguration(true, 1));
var_dump($dao->isWriteAllowedForDate(new DateTime())); //true
var_dump($dao->isWriteAllowedForDate(new DateTime('2000-01-01'))); //false
var_dump($dao->setTaskBlockConfiguration(false, null));
var_dump($dao->isWriteAllowedForDate(new DateTime('2000-01-01'))); //true
