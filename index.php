<?php
	require_once('includes/db_open.php');
	require_once('includes/common.php');
	user_login();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>gahl.dk</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/styles.css" type="text/css" />

	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-34820941-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>

</head>
<body>

<div style="background-image:url('images/graphics/monitor.jpg');width:873px;height:725px;">
	<?php require_once('main.php'); ?>
</div>

<div id="console">
<?php require_once('console.php'); ?>
</div>

    <script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.scrollTo-1.4.3.1-min.js"></script>
    <script type="text/javascript" src="js/console.js"></script>

    <script type="text/javascript">
        var current_user_name = '<?php echo current_user()->name; ?>';
        var last_commands_log_id = 99999999;
        var last_wall_id = 99999999;

				if (window.location.hash) {
					$('#prompt').val(window.location.hash.substr(1));$('form').submit();
				}

        $(function(){
            $('#prompt').focus();

            hook_up_history();

            put_online();
        });
    </script>
</body>
</html>

<?php
	require_once('includes/db_close.php');
?>
