function getControllerPath() {
    let returned = "";
    let str = window.location.pathname;
    let explode = str.split('/');
    if (explode[1] == "en") {
        returned += "en/" + explode[2];
    } else {
        returned = explode[1];
    }
    return returned;
}

function getXMLHttpRequest() {
    var xhr = null;

    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest();
        }
    } else {
        return null;
    }

    return xhr;
}

let url = '/' + getControllerPath();

function addOrRemoveCompetence(competenceId) {
    const xhr = getXMLHttpRequest();
    let url = '/' + getControllerPath();

    xhr.onload = function () {
        if (this.status === 200) {
            let data = JSON.parse(this.responseText);

            if (data.response !== "ok") {
               // alert("Il y a eu une erreur, réessayez...")
            }
            let btn = document.getElementById('btnAddOrRemove'+competenceId)
            let i = document.getElementById('iAddOrRemove'+competenceId)

            if (btn.classList.item(2) === "btn-danger") {
                btn.classList.remove('btn-danger')
                btn.classList.add('btn-success')
                i.classList.remove('ni-fat-remove')
                i.classList.add('ni-check-bold')
            } else {
                btn.classList.remove('btn-success')
                btn.classList.add('btn-danger')
                i.classList.remove('ni-check-bold')
                i.classList.add('ni-fat-remove')
            }
        }
    }

    xhr.open("POST", url + "/addOrRemoveCompetence/");
    let dataToSend = {'competenceId': competenceId};
    xhr.send(JSON.stringify(dataToSend));
}

function addOrRemovePersonnalite(personnaliteId) {
    const xhr = getXMLHttpRequest();
    let url = '/' + getControllerPath();

    xhr.onload = function () {
        if (this.status === 200) {
            let data = JSON.parse(this.responseText);

            if (data.response !== "ok") {
                // alert("Il y a eu une erreur, réessayez...")
            }
            let btn = document.getElementById('btnAddOrRemove'+personnaliteId)
            let i = document.getElementById('iAddOrRemove'+personnaliteId)

            if (btn.classList.item(2) === "btn-danger") {
                btn.classList.remove('btn-danger')
                btn.classList.add('btn-success')
                i.classList.remove('ni-fat-remove')
                i.classList.add('ni-check-bold')
            } else {
                btn.classList.remove('btn-success')
                btn.classList.add('btn-danger')
                i.classList.remove('ni-check-bold')
                i.classList.add('ni-fat-remove')
            }
        }
    }

    xhr.open("POST", url + "/addOrRemovePersonnalite/");
    let dataToSend = {'personnaliteId': personnaliteId};
    xhr.send(JSON.stringify(dataToSend));
}