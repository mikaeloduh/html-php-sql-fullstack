var timeout    = 500;
var closetimer = null;
var menu       = null;

function showTeams() {
    cancelClose();      // keep showing menu

    if (menu)
        menu = menu.style.visiblity = 'hidden';

    menu = document.getElementById('teams');
    menu.style.visibility = 'visible';
}

function close() {
    if (menu) {
        menu.style.visibility = 'hidden';
    }
}

function closeTeams() {
    closetimer = window.setTimeout(close, timeout);
}

function cancelClose() {
    if (closetimer) {
        window.clearTimeout(closetimer);
        closetimer = null;
    }
}

function ddLinkHilite(link) {
    cssClass = link.getAttribute('class') + ' dd-link-hover';
    link.setAttribute('class', cssClass);
}

function ddLinkUnhilite(link) {
    link.setAttribute('class', 'dd-link');
}

function ddLinkToPage(link) {
    teamID = link.getAttribute('id');
    postData = { 'team-id': teamID };
    simFormSubmit('teams.php', document, postData);
}