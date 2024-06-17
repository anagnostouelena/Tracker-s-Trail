let route = [];
const ip = myApp.ip; 
const loading = document.getElementById("loading-circle");

// var json = {
  
//     "group1": {
//         "times": [
//             {
//                 "point1": "17:00"
//             },
//             {
//                 "point2": "18:00"
//             }
//         ]
//     }
    
// }

function refresh() {
    loading.style.display="flex";
    const xhr = new XMLHttpRequest();
    const url = ip+"/game/controlers/getRouties.php";
    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const json = JSON.parse(xhr.responseText);
            loading.style.display="none";
            loadData(json);
        }
    };

    xhr.send();
}

refresh();

function loadData(getJson) {
    const tableBody = document.getElementById('table-data-routes').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";
    for (let i = 0; i < getJson.length; i++) {
        let routeJson = getJson[i];
        const newRow = tableBody.insertRow();
        const routeName = newRow.insertCell(0);
        const routeRow = newRow.insertCell(1);
        const actionRow = newRow.insertCell(2);
        const deleteIcon = document.createElement('img');

        const deleteButton = document.createElement('button');
        deleteButton.id="delete-button";
        deleteButton.className = 'button';
        deleteIcon.className="icon";
        deleteIcon.src="icons/delete.png";
        deleteButton.appendChild(deleteIcon);
        const div = document.createElement('div');
        div.id="button-wrap-div"

        div.appendChild(deleteButton);
        actionRow.appendChild(div);

        //adds all the text fields to the table
        var routeNameText = document.createTextNode(routeJson.route_name);
        routeName.appendChild(routeNameText);

        routeJson.route.forEach(function (point, index){
            const routePointSpan = document.createElement('span');
            routePointSpan.innerHTML = point.toString();
            if (index !== routeJson.route.length-1) {
                routePointSpan.innerHTML +=" > ";
            }

            routeRow.appendChild(routePointSpan);
        });

        deleteButton.addEventListener('click', createDeleteButtonClickHandler(deleteButton, routeJson.route_name));
    }
}

function createDeleteButtonClickHandler(button, route_name) {
    return function () {
        const confirmDelete = confirm("Είστε σίγουρος ότι θέλετε να διαγράψετε την διαφρομη;");
        if (confirmDelete) {
            loading.style.display="flex";


            var json = {};
            json.route_name = route_name;
            const sendJson = JSON.stringify(json);
            console.log(sendJson);
        
            const xhr = new XMLHttpRequest();
            const url = ip + "/game/controlers/deleteRoutes.php";
            xhr.open("POST", url, true);
        
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    loading.style.display="none";
                    refresh();
                }
            };
        
            xhr.send(sendJson);
        }
    }
}

refresh();

function popModal() {
    route = [];
    const inputFields = document.querySelectorAll('input');
    inputFields.forEach(input => {
        input.value = '';
    });

    const pointSpanPs = document.querySelectorAll('point-name-p');
    pointSpanPs.forEach(p => {
        p.style.backgroundColor="#fff";
    });

    const display = document.getElementById("route-points-dis");
    display.innerHTML="";

    loading.style.display="flex";

    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/getPoint.php";
    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const json = JSON.parse(xhr.responseText);
            loading.style.display="none";
            const modalDiv = document.getElementById("addmodal");
            modalDiv.classList.remove('fade-out');
            modalDiv.classList.add('fade-in');
            modalDiv.style.display = 'flex';
            makeRoute(json)
        }
    };

    xhr.send();

    setTimeout(function () {

    }, 1000);
}

function closeModal() {
    const modalDiv = document.getElementById("addmodal");
    modalDiv.classList.remove('fade-in');
    modalDiv.classList.add('fade-out');

    setTimeout(function () {
        modalDiv.style.display = 'none';
        modalDiv.classList.remove('fade-out');
    }, 100);
}

function makeRoute(pointsJson) {
    const pointsDiv = document.getElementById("points");
    pointsDiv.className="route-points";
    pointsDiv.innerHTML="";

    for (let i = 0; i < pointsJson.length; i++) {
        const p = document.createElement("p");
        p.id="point-name-p";
        p.innerHTML=pointsJson[i].point_name;
        p.addEventListener('click', createPClickHandler(p, pointsJson[i].point_name));
        pointsDiv.appendChild(p);
    }
}

function submitRoute() {
    loading.style.display="flex";
    const routeName = document.getElementById("route-name").value;
    var json = {};
    json.route_name = routeName;
    json.points = route;
    const jsonString = JSON.stringify(json);
    console.log(jsonString);

    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/postRouties.php";
    xhr.open("POST", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const json = JSON.stringify(xhr.responseText);
            loading.style.display="none";
            closeModal("edit-team");
            refresh();
        }
    };

    xhr.send(jsonString);

}

function createPClickHandler(p, point) {
    const display = document.getElementById("route-points-dis");
    const clickHandler = function () {
        p.disabled = true;
        route.push(point);
        const span = document.createElement("span");
        span.id = "point-name";
        span.innerHTML = point;
        p.style.backgroundColor = "#555";
        display.appendChild(span);
        // Disable the click listener
        p.removeEventListener("click", clickHandler);
    };
    return clickHandler;
}
