<div id="logo"><?php echo current_user()->name ?>@gahl.dk</div>

<div id="output"></div>

<div id="input"></div>

<div id="wall">
	<h1>VÆG</h1>
	<div id="wall_posts">
		<?php
			$query = "select p.name, w.ID, w.Post, DATE_FORMAT(w.date, '%d.%m.%y %H:%i') as formated_date from wall w inner join people p on w.people_id=p.id order by date DESC limit 20";
			$result = mysql_query($query) or die(mysql_error());
			while($row = mysql_fetch_assoc($result)) { ?>
			
				<div><b><?php echo $row['name']; ?></b> - <i><?php echo $row['formated_date']; ?></i><br /> <?php echo $row['Post']; ?></div>
				<hr />
				<? if($row['ID']>$last_wall_id) $last_wall_id = $row['ID']; ?>
			<? } 
		?>
	</div>
</div>
<script type="text/javascript">last_wall_id = <?php echo $last_wall_id ?>;</script>