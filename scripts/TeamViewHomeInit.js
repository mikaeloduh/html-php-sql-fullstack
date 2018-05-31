(function ($) {

    // Set team name and format page title region
	var titleRegion = $('.team-title');
        titleSpan = document.createElement('span');
    titleSpan.innerHTML = team['name'];
    titleRegion.append(titleSpan);
    titleRegion.width($(titleSpan).width());

    // Populate coaches region with content
    var coachesRegion = document.getElementById('team-coaches-info');
    for (var coach in coaches) {
        if (coaches.hasOwnProperty(coach)) {
            var coachContainer = document.createElement('div'),
                portraitPanel = document.createElement('div'),
                infoPanel = document.createElement('div'),
                nameSpan = document.createElement('span'),
                lineBreak = document.createElement('br');
            portraitPanel.setAttribute('class', 'inline-portrait');
            nameSpan.setAttribute('class', 'generated-content-header medium-print');
            nameSpan.innerHTML = coaches[coach]['first_name'] + ' ' + coaches[coach]['last_name'];
            infoPanel.appendChild(nameSpan);
            infoPanel.appendChild(lineBreak);
            infoPanel.innerHTML += coaches[coach]['email'] + '<br />(' + coaches[coach]['phone'] + ')';
            infoPanel.setAttribute('class', 'small-print');
            coachContainer.appendChild(portraitPanel);
            coachContainer.appendChild(infoPanel);
            coachContainer.style.height = 70 + 'px';
            coachesRegion.appendChild(coachContainer);
        }
    }

    // Fine-tune coaches region formatting and set appropriate panel label
    if (coaches.length > 1)
        document.getElementById('coach-panel-label').innerHTML = 'Coaches';
    else
        document.getElementById('coach-panel-label').innerHTML = 'Coach';
    if (coaches.length > 2)
        coachesRegion.classList.add('border-bottom-switch');

    // Populate schedule area
    var scheduleRegion = document.getElementById('team-schedule');
    for (var time in practice) {
        if (practice.hasOwnProperty(time)) {
            scheduleRegion.innerHTML += practice[time]['pr_day'] + ', ' + practice[time]['pr_time'] + '<br />';
        }
    }

    // Populate announcements area
    var announcementsRegion = document.getElementById('team-announcements');
    for (var announcement in announcements) {
        if (announcements.hasOwnProperty(announcement)) {
            var container = document.createElement('div'),
                timestamp = document.createElement('div');
            timestamp.setAttribute('class', 'header-text border-bottom-switch');
            timestamp.innerHTML = announcements[announcement]['ann_time'];
            container.setAttribute('class', 'generated-text-region');
            container.appendChild(timestamp);
            container.innerHTML += announcements[announcement]['message'];
            announcementsRegion.appendChild(container);
        }
    }
    
    // Populate player roster panel
    for (var player in players) {
    	if (players.hasOwnProperty(player)) {
    		$('#player-roster').append(
    			"<div style='display: table-row; height: 40px;'>" +
    				"<div style='display: table-cell; width: 230px; border-bottom: 1px solid #dce9f9'>" +
    					"<span class='generated-content-header medium-print'>" +
    						players[player]['player_name'] + "</span><br />" +
    					players[player]['parent_name'] + "&nbsp;(" + players[player]['phone'] + ")" +
    				"</div>" +
    				"<div style='display: table-cell; width: 50px; text-align: center; border-bottom: 1px solid #dce9f9'>" +
    					players[player]['number'] +
    				"</div>" +
    			"</div>"
    		);
    	}
    }
    
    // Populate next game panel
    if (typeof(nextGame) != 'undefined') {
    	
    } else {
    	$('#next-game').append(
    		"<span style='padding-left: .5em;'>To Be Announced...</span>"
    	);
    }
    
    // Attach button click event handlers
    $('.mini-round-button').click(function () {
    	simFormSubmit('teams.php', document, { 'sel-team-id': team['team_id'] });
    });

    // Center team content page after it finishes loading
    var teamPage = $('.team-content-main').width(860);

})(jQuery);