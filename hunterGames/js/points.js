const ip = myApp.ip; 
const loading = document.getElementById("loading-circle");
let getJson = [
    {
        "point_name":"aeifha",
        "judgment":"lahflafaf"
    }
]
// loadData(getJson);


function refresh() {
    loading.style.display="flex";
    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/getPoint.php";
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



function popModal() {
    const inputFields = document.querySelectorAll('input');

    
    inputFields.forEach(input => {
        input.value = '';
    });
    const modalDiv = document.getElementById("addpointsmodal");
    modalDiv.classList.remove('fade-out');
    modalDiv.classList.add('fade-in');
    modalDiv.style.display = 'flex';
}

function closeModal() {
    const modalDiv = document.getElementById("addpointsmodal");
    modalDiv.classList.remove('fade-in');
    modalDiv.classList.add('fade-out');


    setTimeout(function () {
        modalDiv.style.display = 'none';
        modalDiv.classList.remove('fade-out');
    }, 100);
}



function createButtonClickHandler(button, point) {
    return function () {
        button.disabled = true;

        setTimeout(function () {
            button.disabled = false;
        }, 300);
    };
}
   
function loadData(getJson) {
    loading.style.display="none";
    const tableBody = document.getElementById('table-data-points').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";

    for (let i = 0; i < getJson.length; i++) {
        let pointJson = getJson[i];
        const newRow = tableBody.insertRow();
        const pointName = newRow.insertCell(0);
        const pointjudge = newRow.insertCell(1);
        const buttonCell = newRow.insertCell(2);

        //adds all the text fields to the table

        var pointNameText = document.createTextNode(pointJson.point_name);
        pointName.appendChild(pointNameText);

        var pointNameText = document.createTextNode(pointJson.judgment+"\n");
        var judgementPhone = document.createTextNode(pointJson.judgment_phone);
        pointjudge.appendChild(pointNameText);
        pointjudge.appendChild(judgementPhone);

        const deleteButton = document.createElement('button');
        const deleteIcon = document.createElement('img');
        deleteIcon.className="icon";
        deleteIcon.src="icons/delete.png";

        deleteButton.className = 'button';
        deleteButton.id="delete-button";
        deleteButton.appendChild(deleteIcon);

        const div = document.createElement('div');
        div.style.marginTop="10px";
        div.style.marginBottom="-10px";
        div.appendChild(deleteButton)


        deleteButton.addEventListener('click', createDeleteButtonClickHandler(deleteButton, pointJson));
        buttonCell.appendChild(div);
    }
}

function createDeleteButtonClickHandler(button, point) {
    return function () {
        const confirmDelete = confirm("Είστε σίγουρος ότι θέλετε να διαγράψετε το σημείο;");
        if (confirmDelete) {
            loading.style.display="flex";

            var pointName = point;
            var json = {};
            json.point_name = pointName.point_name;
            const sendJson = JSON.stringify(json);
            console.log(sendJson);
        
            const xhr = new XMLHttpRequest();
            const url = ip + "/game/controlers/deletePoint.php";
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

function submitPoint() {
    loading.style.display="flex";

    var pointName =  document.getElementById('point_name').value;
    var pointJudge =  document.getElementById('judge_name').value;
    var pointJudgePhone =  document.getElementById('judge_phone').value;
    var pointJudgeEmail = document.getElementById('judge_email').value;

    var json = {};
    json.point_name = pointName;
    json.judgment = pointJudge;
    json.judgment_phone = pointJudgePhone;
    json.judgment_email = pointJudgeEmail;

    var sendJson = JSON.stringify(json, null, 2);

    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/postPoint.php";
    xhr.open("POST", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const json = JSON.stringify(xhr.responseText);
            loading.style.display="none";
            closeModal("edit-team");
            refresh();
        }
    };

    xhr.send(sendJson);

}
    
