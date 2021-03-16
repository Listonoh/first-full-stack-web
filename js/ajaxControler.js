
function getRow(element) {
    //probably should be done better 
    return element.parentElement.parentElement

}

/**
    * @param {String} tag = ["hidden" | "visible"]
    * @param {String} visibility = ["none" | "block"]
    */
function setVisibility(element, tag, visibility = "none") {
    [...element.getElementsByClassName(tag)]
        .forEach(element => element.style.display = visibility);
}

function changeToInput(event) {
    var row = getRow(event.target);
    setVisibility(row, "hidden", "block");
    setVisibility(row, "visible", "none");
    row.getElementsByClassName("change-amount")[0].focus();
}

function changeToNormal(event) {
    var row = getRow(event.target);
    const url = new URL('sqlHandler.php', window.location.href);
    const newAmount = row.getElementsByClassName("change-amount")[0].value;
    const query = "/?action=change&id=" + event.target.id.split("-")[1] + "&amount=" + newAmount;

    fetch(url.href + query, { method: "POST" })
        .then((response) => {
            if (response.ok) {
                setVisibility(row, "hidden", "none");
                setVisibility(row, "visible", "block");
                row.getElementsByClassName("amount")[0].innerText = newAmount;
            }
        })
        .catch((er) => { alert(er); console.log(er) });
}

function deleteRecord(event) {
    const url = new URL('sqlHandler.php', window.location.href);
    var row = getRow(event.target);

    fetch(url.href + "?id=" + event.target.id.split("-")[1], { method: "DELETE" })
        .then(() => {
            row.parentNode.removeChild(row);
        })
}

function getId(row) {
    return row.getElementsByClassName("btn-delete")[0].id.split("-")[1]
}

function switchTwoRows(row1, row2) {
    const url = new URL('sqlHandler.php', window.location.href);
    const query = "/?action=switch&id1=" + getId(row1) + "&id2=" + getId(row2);
    fetch(url.href + query, { method: "POST" })
        .then((response) => {
            if (response.ok) {
                row1.parentElement.insertBefore(row1, row2)
            }
        })
        .catch((er) => { alert(er); console.log(er) });
}

function switchUp(event) {
    row = getRow(event.target);
    if (row.parentNode.rows[row.rowIndex - 2]) {
        switchTwoRows(row, row.parentNode.rows[row.rowIndex - 2])
    }
}

function switchDown(event) {
    row = getRow(event.target);
    if (row.parentNode.rows[row.rowIndex]) {
        switchTwoRows(row.parentNode.rows[row.rowIndex], row)
    }
}

function sortContend(event){
    console.log(event.target.id);
    action = event.target.id.split("-")[1];
    const url = new URL('sqlHandler.php', window.location.href);
    const query = "/?action=" + action;
    fetch(url.href + query, { method: "POST" })
        .then((response) => {
            if (response.ok) {
                location.reload();
            }
        })
        .catch((er) => { alert(er); console.log(er) });

}

function asignEventListener(to, eventFunction) {
    var tabLinks = document.getElementsByClassName(to);
    for (var i = 0; i < tabLinks.length; i++) {
        tabLinks[i].addEventListener("click", function (event) {
            event.preventDefault();
            eventFunction(event);
        })
    }
}

window.addEventListener('load', function () {
    setVisibility(document, "hidden", "none");

    asignEventListener("btn-delete", deleteRecord);
    asignEventListener("btn-change", changeToInput);
    asignEventListener("btn-apply", changeToNormal);
    asignEventListener("btn-switch-up", switchUp);
    asignEventListener("btn-switch-down", switchDown);
    asignEventListener("btn-sort", sortContend);

});