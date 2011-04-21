<?php
/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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
 *
 *
 * This file is based on code from WordPress project: www.wordpress.org
 */

/** Web installation wizard: welcome screen.
 *
 * This file is based on code from WordPress project: www.wordpress.org
 *
 * @filesource
 * @package PhpReport
 * @subpackage install
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Welcome to PhpReport</title>
    <link rel="stylesheet" href="css/install.css" type="text/css" />
</head>
<body><h1 id="logo"><img alt="PhpReport" src="images/phpreport-logo.png" /></h1>
    <p>I need some data about your database, and its file (<code>config.php</code>) doesn't seem to exist. You can create a <code>config.php</code> file through a web interface, or you can edit it manually. The choice is yours!<br><br></p><p><a href='setup-config.php' class='button'>Create a Configuration File</a></p>
</body>
</html>
