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

  header('Content-Type: text/html; charset=UTF-8');
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head>

    <title><?php echo PAGE_TITLE; ?></title>

    <!-- Include PhpReport stylesheet -->
    <link rel="stylesheet" type="text/css" href="include/phpreport.css">

    <!-- Include Ext stylesheet -->
    <link rel="stylesheet" type="text/css" href="ext/resources/css/ext-all.css">
    <link rel="stylesheet" type="text/css" href="include/silk.css" />
    <link rel="stylesheet" type="text/css" href="include/tools.css" />
    <link rel="stylesheet" type="text/css" href="ext/examples/ux/css/ux-all.css"/>

    <!-- Include DatePickerPlus stylesheet -->
    <link rel="stylesheet" type="text/css" href="include/ext.ux.datepickerplus/datepickerplus.css"/>

		<!-- Include Ext library -->
    <script type="text/javascript" src="ext/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="ext/ext-all.js"></script>
    <script type="text/javascript" src="include/App.js"></script>
    <script type="text/javascript" src="include/RowEditor.js"></script>
    <script type="text/javascript" src="include/DatePickerLinks.js"></script>

        <!-- Configure prototypes -->
    <script type="text/javascript">
      Ext.DatePicker.prototype.startDay = 1;
    </script>

        <!-- Include other common validations -->
    <script type="text/javascript" src="include/validations.js"></script>

        <!-- Include other common functions -->
    <script type="text/javascript" src="include/functions.js"></script>

</head>

<body>

    <div id="header">
        <img alt="PhpReport" src="include/images/phpreport-logo.png" />
        <div id="menubar"></div>
    </div>
