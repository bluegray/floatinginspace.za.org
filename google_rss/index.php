<?php include('../doctype.php'); ?>
<head>
  <title>GoogleBot RSS python script</title>
  <meta name="description" content="Python script to analyze apache log files and report GoogleBot visits in an RSS feed." />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../style.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="python.css" type="text/css" media="screen" />
</head>
<body>
<?php include('../header_ad.php'); ?>
<?php include_once "../phpmarkdown/markdown.php"; ?>
<div class='content'>
<?php  $lyr1 = <<<LYR1

# GoogleBot RSS feed script
## The Python script
This python script will go through your apache access log and make an rss feed of the latest crawls by GoogleBot, or any other string you want to keep an eye on. Change the maximum number of entries displayed to suit your needs. The time_adjust variable will add the amount in hours to the displayed log time. This is useful if the server is not in your timezone. It might be a good idea to password protect this feed, you probably don't want everyone to see it.

python script: [googlebot.rss](google_rss_script.php "python script")

LYR1;
$my_html = Markdown($lyr1); print  $my_html ?>

  <div class="code">  <?php include('google_rss.php'); ?></div>
  <?php include('license.php'); ?>  
</div>
  <?php include('../footer.php'); ?>
</body>
</html>
