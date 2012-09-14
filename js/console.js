function command(prompt_element) {

	if(prompt_element.value == '')
		return false;

	print_input(prompt_element.value, current_user_name);	
	
	var url = '/ajax.php?fkt=command&command=' + encodeURIComponent(prompt_element.value);
	new Ajax.Request(url, {
		method: 'post',
		onSuccess: function(transport) {
			print_output(transport.responseText)
		}
	});
			
	prompt_element.value = '';
	
}

/** Putter alle elementer, der skal lava AJAX callbacks online (i.e. #input og #wall) */
function put_online()
{
	new Ajax.Request('ajax.php?fkt=update_console_and_wall&last_commands_log_id='+last_commands_log_id+"&last_wall_id="+last_wall_id, {
		onSuccess: function(transport) { 
			data = eval(transport.responseText);
			wall_data = data[0];
			console_data = data[1];
			
			// Wall data handling
			if(wall_data.length>0) {
				last_wall_id = wall_data[0];

				var wall_posts = wall_data[1];
				for(i=0; i<wall_posts.length; i++) {
					print_on_wall(wall_posts[i][0], wall_posts[i][1], wall_posts[i][2]);
				}
			}

			// Console input data handling
			if(console_data.length>0) {
				last_commands_log_id = console_data[0];

				var commands = console_data[1];
				for(i=0; i<commands.length; i++) {
					print_input(commands[i][0], commands[i][1]);
				}
			}
			window.setTimeout(put_online, 1000);
		}
	} );
}

function print_output(content) {
	to_print = 'SYSTEM >> ' + content + '<br />';

	$('output').insert(to_print);
	//$('output').scrollTo('bottom');
	$('output').scrollTop = $('output').scrollHeight;
}

function print_input(content, username) {
	to_print = '' + username + ' >> ' + content + '<br />';

	$('input').insert(to_print);
	//$('input').scrollTo('bottom');	
	$('input').scrollTop = $('input').scrollHeight;
}

function print_on_wall(date, post, name) {
	to_print = '<div><b>' + name + '</b> - <i>' + date + '</i><br />' + post + '</div><hr />';
	$('wall_posts').insert({top:to_print});
}