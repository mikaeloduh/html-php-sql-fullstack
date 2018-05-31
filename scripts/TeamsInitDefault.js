var onClickHandler = function (link) {
        var selectionId = $(link).parent().attr('id'),
            teamId = selectionId.split('-')[1],
            postData = { 'sel-team-id': teamId };

        // Prevent onclick from firing if already selected
        if (typeof selected != 'undefined') {
            var selectedTeamId = $(link).parent().attr('id').split('-')[1];
            if (selectedTeamId == selected)
                return;
        }

        simFormSubmit('teams.php', document, postData);
    },
    onClickHandlerQL = function (link) {
    	var selectionId = link.getAttribute('id'),
    		teamId = selectionId.split('-')[2],
    		postData = { 'team-id': teamId };
        simFormSubmit('teams.php', document, postData);
    };

(function ($) {

    var onMouseOverHandler = function (event) {
            var target = event.currentTarget,
                parent = target.parentElement,
                targetStyle = target.getAttribute('class') + ' team-select-label-hilited',
                parentStyle = parent.getAttribute('class') + ' select-container-hilited';

            // Prevent onmouseover from firing if already selected
            if (typeof selected != 'undefined') {
                var selectedTeamId = target.getAttribute('id').split('-')[1];
                if (selectedTeamId == selected)
                    return;
            }

            target.setAttribute('class', targetStyle);
            parent.setAttribute('class', parentStyle);
        },
        onMouseOutHandler = function (event) {
            var target = event.currentTarget,
                parent = target.parentElement;

            // Prevent onmouseout from firing if already selected
            if (typeof selected != 'undefined') {
                var selectedTeamId = target.getAttribute('id').split('-')[1];
                if (selectedTeamId == selected)
                    return;
            }

            target.setAttribute('class', 'team-select-label');
            parent.setAttribute('class', 'select-container');
        };

    // Populate selection panel with team entries
    var selectionPanel = document.getElementById('team-select-panel');
    for (var team in teams) {
        if (teams.hasOwnProperty(team)) {
            /* Create new div container element for selection and child
               elements for main selection and link to page actions */
            var container = document.createElement('div'),
                mainSelect = document.createElement('div'),
                quickSelect = document.createElement('div');

            // Set container attributes
            container.setAttribute('class', 'select-container');

            // Set main selection area attributes and event handlers
            mainSelect.setAttribute('id', 'sel-' + team);
            mainSelect.setAttribute('class', 'team-select-label');
            mainSelect.onmouseover = onMouseOverHandler;
            mainSelect.onmouseout = onMouseOutHandler;
            mainSelect.innerHTML =	'<div style="display: table-cell; width: 120px;" onclick="onClickHandler(this);">' + teams[team] + '</div>' + 
            						'<div style="display: table-cell">' +
            							'<div id="quick-sel-' + team + '" class="mini-round-button" onclick="onClickHandlerQL(this);">>></div>' +
            						'</div>';

            // Attach new elements to their parent elements
            container.appendChild(mainSelect);
            selectionPanel.appendChild(container);
        }
    }

    // If team brief selected then populate brief view panel with content
    if (typeof teamData != 'undefined') {
        /* Set selection style for team in selection panel */

        var selectTarget = document.getElementById('sel-' + selected),
            selectParent = selectTarget.parentElement,
            targetStyle = selectTarget.getAttribute('class') + ' team-select-label-selected',
            parentStyle = selectParent.getAttribute('class') + ' select-container-selected';
        selectTarget.setAttribute('class', targetStyle);
        selectParent.setAttribute('class', parentStyle);

        /* Delete default brief panel content */

        var briefPanel = document.getElementById('team-brief-panel');
        while (briefPanel.firstChild) {
            briefPanel.removeChild(briefPanel.firstChild);
        }

        /* Create coaches listing (there may be multiple coaches for a team) */

        var coachesContainer = document.createElement('div'),   // Coach listing container node
            coachesHeader = document.createElement('div');      // Coach listing header node

        // Create appropriate coaches heading text
        coachesHeader.setAttribute('class', 'generated-content-header');
        coachesHeader.innerHTML = 'Coach';
        if (coaches.length > 1)
            coachesHeader.innerHTML += 'es';
        coachesHeader.innerHTML += ':';

        // Set container attributes and append child header node
        coachesContainer.setAttribute('class', 'generated-content-group');
        coachesContainer.appendChild(coachesHeader);

        // Create coaches listing elements
        for (var coach in coaches) {
            if (coaches.hasOwnProperty(coach)) {
                var coachLine = document.createElement('div'),      // Coach line container node
                    coachName = document.createElement('div'),      // Generated coach name line child node
                    coachEmail = document.createElement('div');     // Generated coach email line child node
                coachLine.setAttribute('class', 'generated-content-line');

                // Set coach name node attributes and content
                coachName.setAttribute('class', 'generated-content-column');
                coachName.setAttribute('style', 'padding-left: .45em;')
                coachName.innerHTML = coaches[coach]['coach_name'];

                // Set coach email node attributes and content
                coachEmail.setAttribute('class', 'generated-content-column');
                coachEmail.innerHTML = '(' + coaches[coach]['email'] + ')';

                // Attach child nodes to line, attach line node to container
                coachLine.appendChild(coachName);
                coachLine.appendChild(coachEmail);
                coachesContainer.appendChild(coachLine);
            }
        }

        // Attach coaches listing element to brief panel
        briefPanel.appendChild(coachesContainer);

        /* Create additional overview information content */

        var supplContainer = document.createElement('div'),     // Supplemental info container
            homeLine = document.createElement('div'),           // Home park line container
            divisionLine = document.createElement('div'),       // Division line container
            foundedLine = document.createElement('div');        // Date team formed line container

        supplContainer.setAttribute('style', 'display: table;');
        homeLine.setAttribute('style', 'display: table-row;')
        divisionLine.setAttribute('style', 'display: table-row;');
        foundedLine.setAttribute('style', 'display: table-row;');
        
        // Create home park label and content nodes, append to line container and panel nodes
        var homeLabel = document.createElement('div'),
            homeCourt = document.createElement('div');
        homeLabel.setAttribute('class', 'generated-content-label');
        homeLabel.setAttribute('style', 'text-align: right;')
        homeLabel.innerHTML = 'Regional Home:';
        homeCourt.setAttribute('class', 'generated-content-column');
        homeCourt.innerHTML = teamData['home_court'];
        homeLine.appendChild(homeLabel);
        homeLine.appendChild(homeCourt);
        supplContainer.appendChild(homeLine);

        // Create division label and content nodes, append to line container and panel nodes
        var divisionLabel = document.createElement('div'),
            division = document.createElement('div');
        divisionLabel.setAttribute('class', 'generated-content-label');
        divisionLabel.setAttribute('style', 'text-align: right;')
        divisionLabel.innerHTML = 'Division:';
        division.setAttribute('class', 'generated-content-column');
        division.innerHTML = teamData['division'];
        divisionLine.appendChild(divisionLabel);
        divisionLine.appendChild(division);
        supplContainer.appendChild(divisionLine);

        // Create founded label and content nodes, append to line container and panel nodes
        var foundedLabel = document.createElement('div'),
            founded = document.createElement('div');
        foundedLabel.setAttribute('class', 'generated-content-label');
        foundedLabel.setAttribute('style', 'text-align: right;');
        foundedLabel.innerHTML = 'Playing Since: ';
        founded.setAttribute('class', 'generated-content-column');
        founded.innerHTML = teamData['founded'];
        foundedLine.appendChild(foundedLabel);
        foundedLine.appendChild(founded);
        supplContainer.appendChild(foundedLine);

        // Set supplemental info container and attach to brief panel
        supplContainer.setAttribute('class', 'generated-content-group')
        briefPanel.appendChild(supplContainer);

        /* Create games played panel */
        
        // Create panel header and table header
        $(briefPanel).append(
        	'<div class="content-panel-header">' +
        		'Current Season Games' +
        	'</div>'
        );
        var tableStr = '<div class="small-print" style="margin: .5em auto 0 auto; width: 320px;">' +
    						'<div style="display: table-row; background-color: #dce9f9; border-radius: 4px; font-weight: bold;">' +
    							'<div style="display: table-cell; padding: .3em .5em;">Score</div>' +
    							'<div style="display: table-cell; padding: .3em .5em;">Against</div>' +
    							'<div style="display: table-cell; padding: .3em .5em;">Location</div>' +
    							'<div style="display: table-cell; padding: .3em .5em;">Date</div>' +
    						'</div>';
        
        // Create table contents
        for (var i = 0; i < gameStats.length; ++i) {
        	var row = gameStats[i],
        		score = null,
        		against = null,
        		location = null;
        	
        	// Determine if home or away game and set stats
        	if (gameStats[i]['home_TID'] == selected) {
        		score = gameStats[i]['home_score'];
        		against = gameStats[i]['away_team'];
        		location = 'Home';
        	} else {
        		score = gameStats[i]['away_score'];
        		against = gameStats[i]['home_team'];
        		location = 'Away';
        	}
        	
        	tableStr += '<div style="display: table-row;">';	// Row start
        	
        	// Score column
        	tableStr +=		'<div style="display: table-cell; padding: .2em .5em; ';
        	if (i < gameStats.length - 1)
        		tableStr += 	'border-bottom: 1px solid #dce9f9;';
        	tableStr += 		'">' + score + '</div>';
        	// Against column
        	tableStr +=		'<div style="display: table-cell; padding: .2em .5em; ';
        	if (i < gameStats.length - 1)
        		tableStr += 	'border-bottom: 1px solid #dce9f9;';
        	tableStr += 		'">' + against + '</div>';
        	// Location column
        	tableStr +=		'<div style="display: table-cell; padding: .2em .5em; ';
        	if (i < gameStats.length - 1)
        		tableStr += 	'border-bottom: 1px solid #dce9f9;';
        	tableStr += 		'">' + location + '</div>';
        	// Date column
        	tableStr +=		'<div style="display: table-cell; padding: .2em .5em; width: 200px; ';
        	if (i < gameStats.length - 1)
        		tableStr += 	'border-bottom: 1px solid #dce9f9;';
        	tableStr += 		'">' + gameStats[i]['date'] + '</div>';
        	
        	tableStr += '</div>';	// Row end
        }
        tableStr += '</div></div>';
        $(briefPanel).append(tableStr);
    }
    
    // Center content
    var mainContentRegion = document.getElementsByClassName('team-content-main')[0];
    var width = (document.getElementById('team-select-panel').scrollWidth + document.getElementById('team-brief-panel').scrollWidth) * 1.15;
    mainContentRegion.style.width = width + 'px';

})(jQuery);