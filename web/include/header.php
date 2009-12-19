<?php
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

		<!-- Include Ext library -->
    <script type="text/javascript" src="ext/adapter/ext/ext-base-debug.js"></script>
    <script type="text/javascript" src="ext/ext-all-debug.js"></script>
    <script type="text/javascript" src="ext/examples/shared/extjs/App.js"></script>
    <script type="text/javascript" src="include/RowEditor.js"></script>

        <!-- Configure prototypes -->
    <script type="text/javascript">
      Ext.DatePicker.prototype.startDay = 1;
    </script>

        <!-- Include other common validations -->
    <script type="text/javascript" src="include/validations.js"></script>

</head>

<body>

    <div id="header">
        <h1>PhpReport</h1>
    </div>
