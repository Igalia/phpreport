<?php
/*
 * Copyright (C) 2009-2018 Igalia, S.L. <info@igalia.com>
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
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">

  <title>
    <?php echo PAGE_TITLE; ?>
  </title>

  <!-- Include PhpReport stylesheet -->
  <link rel="stylesheet" type="text/css" href="include/phpreport.css">

  <!-- Include Ext stylesheet -->
  <link rel="stylesheet" type="text/css" href="ext/resources/css/ext-all.min.css">
  <link rel="stylesheet" type="text/css" href="include/silk.css" />
  <link rel="stylesheet" type="text/css" href="include/tools.css" />
  <link rel="stylesheet" type="text/css" href="ext/examples/ux/css/ux-all.css" />

  <!-- Include DatePickerPlus stylesheet -->
  <link rel="stylesheet" type="text/css" href="include/ext.ux.datepickerplus/datepickerplus.css" />

  <!-- Include Ext library -->
  <script src="ext/adapter/ext/base.js"></script>
  <script src="ext/ext.js"></script>
  <script src="include/App.js"></script>

  <!-- Configure prototypes -->
  <script>
    Ext.DatePicker.prototype.startDay = 1;
  </script>

  <!-- Include other common validations -->
  <script src="include/validations.js"></script>

  <!-- Include improved calendar widget -->
  <script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus.js"></script>

  <!-- Periodical check for open sessions -->
  <script src="js/include/sessionTracker.js"></script>

  <!-- Favicon / shortcut icon configuration -->
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="mask-icon" href="/assets/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#dadada">
  <meta name="theme-color" content="#dadada">

</head>

<body>

  <?php include("include/menubar.php"); ?>
  <script>
    var HEADER_HEIGHT = document.getElementById('menubar').scrollHeight;
  </script>