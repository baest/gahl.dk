var inputHistory = [],
    historyIndex = 0;

function command(prompt_element) {
    var $p = $(prompt_element),
        val = $p.val();

	if(val == '')
		return false;

	print_input(val, current_user_name);

	var url = '/ajax.php?fkt=command&command=' + encodeURIComponent(val);

    $.ajax({
        method: 'post',
        url: url,
        success: function(data) {
            print_output(data)
        }
    });

    inputHistory.push(val);
    historyIndex =  inputHistory.length;

	$p.val('');
}

/** Putter alle elementer, der skal lava AJAX callbacks online (i.e. #input og #wall) */
function put_online()
{
    $.ajax({
        method: 'get',
        url: 'ajax.php?fkt=update_console_and_wall&last_commands_log_id='+last_commands_log_id+"&last_wall_id="+last_wall_id,
        success: function(transport) {
            data = eval(transport.responseText);

            if (!data) return;

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
    });
}

function hook_up_history(){
    $('#prompt').on('keydown', function(e){
        var $p = $(this);

        switch (e.keyCode) {
            case 40: // down
                historyIndex = historyIndex +1;
                historyIndex = historyIndex > inputHistory.length ? inputHistory.length : historyIndex;

                $p.val(inputHistory[historyIndex]);
                break;
            case 38: // up
                historyIndex = historyIndex -1;
                historyIndex = historyIndex < 0 ? 0 : historyIndex;

                $p.val(inputHistory[historyIndex]);
                break;
        }
    });
}

function print_output(content) {
	to_print = 'SYSTEM >> ' + content + '<br />';

	$('#output').append(to_print);
	//$('output').scrollTo('bottom');
	$('#output')[0].scrollTop = $('#output')[0].scrollHeight;
}

function print_input(content, username) {
	to_print = '' + username + ' >> ' + content + '<br />';

	$('#input').append(to_print);
	//$('input').scrollTo('bottom');	
	$('#input')[0].scrollTop = $('#input')[0].scrollHeight;
}

function print_on_wall(date, post, name) {
	to_print = '<div><b>' + name + '</b> - <i>' + date + '</i><br />' + post + '</div><hr />';
	$('#wall_posts').prepend(to_print);
}