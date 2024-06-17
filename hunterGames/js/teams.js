const ip = myApp.ip; 
refresh();
let groupNames = [];
function refresh() {
    const loading = document.getElementById("loading-circle");
    loading.style.display="flex";
    var teamStatusElement = document.getElementById('secretVariable');
    var teamStatusElementValue = teamStatusElement.value;
    const xhr = new XMLHttpRequest();
    var pathname = window.location.pathname;
    var filename = pathname.split('/').pop();
    let teamStatus = "";
    
    if (filename === "big_teams.html") {
        teamStatus = "big"
    } else {
        teamStatus = "small"
    }
    
    const url = ip + "/game/controlers/getTeams.php?group_status="+teamStatus;
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

const loading = document.getElementById("loading-circle");
let iterations = 0;
let teamHead = document.createElement("div");
teamHead.className = "members";
let p = document.createElement("p");
p.innerHTML = "Ομαδάρχης";
p.className = "outer-text";
teamHead.appendChild(p);

let membersInner = document.createElement("div");
membersInner.className = "members";
p = document.createElement("p");
p.innerHTML = "Υπόλοιπα Μέλη";
p.className = "outer-text";
membersInner.appendChild(p);
const inputFields = document.querySelectorAll('input');
inputFields.forEach(function(input) {
    input.value="";
});

function send(button) {
    const category = document.getElementById('category').innerHTML;
    let i;
    const textFields = document.querySelectorAll('input');
    const loading = document.getElementById("loading-circle");

    const backButton = document.getElementById("back");
    const jsonData = {};
    const teamMembers = [];
    jsonData.group_name=document.getElementById("team_name").value;
    jsonData.group_status = category;

    const names = document.getElementsByClassName("member-name");
    const emails = document.getElementsByClassName("member-email");
    const phones = document.getElementsByClassName("member-phone");

    for (i = 0; i < names.length; i++) {
        const member = {};
        member.member_name = names[i].value;
        member.member_phone = phones[i].value;
        member.member_email = emails[i].value;
        teamMembers.push(member);
    }

    jsonData.group_members = teamMembers;
    const jsonString = JSON.stringify(jsonData, null, 2);
    console.log(jsonString);
    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/postData.php";
    xhr.open("POST", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const json = JSON.stringify(xhr.responseText);
            loading.style.display="none";
            button.disabled = false;
            backButton.disabled = false;

            for (let i = 0; i < textFields.length; i++) {
                textFields[i].disabled = false;
            }
            console.log(json);

            const modal = new Modal();
            modal.closeModal("addmodal");
            refresh();
        }
    };

    xhr.send(jsonString);
    button.disabled = true;
    backButton.disabled = true;

    loading.style.display="flex";

    for (i = 0; i < textFields.length; i++) {
        textFields[i].disabled = true;
    }
}

let currentIndex = 0;
const totalSlides = 2;
let numberOfFields = 0;

function next() {
    createInputFields();
    nextSlide();
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % totalSlides;
    updateSlides();
}

function updateSlides() {

    const translateValue = -currentIndex * 100 + '%';
    document.getElementById('slide-container').style.transform = 'translateX(' + translateValue + ')';
}

function createInputFields() {
    const number = document.getElementById("members_num").value;
    const memberFields = document.getElementById("member_fields");

    if (number > 0 && numberOfFields !== number) {

        const fieldsToCreate = number - numberOfFields;
        numberOfFields = number;
        if (fieldsToCreate > 0) {
            for (let i = 0; i < fieldsToCreate; i++) {

                const memberDiv = document.createElement("div");

                memberDiv.className = "member-div";
                const memberEmail = document.createElement("input");
                const memberPhone = document.createElement("input");
                const memberName = document.createElement("input");

                memberName.type = "text";
                memberEmail.type = "email";
                memberPhone.type = "text";

                memberName.className = "member-name"
                memberEmail.className = "member-email"
                memberPhone.className = "member-phone"

                if (i === 0  && iterations === 0) {
                    iterations++;
                    memberName.placeholder = "Όνομα Ομαδάρχη";
                    memberEmail.placeholder = "Email Ομαδάρχη";
                    memberPhone.placeholder = "Τηλέφωνο Ομαδάρχη";
                    teamHead.appendChild(memberName);
                    teamHead.appendChild(memberEmail);
                    teamHead.appendChild(memberPhone);

                    memberDiv.appendChild(teamHead);
                }
                else {

                    memberName.placeholder = "Όνομα Μέλους";
                    memberEmail.placeholder = "Email Μέλους";
                    memberPhone.placeholder = "Τηλέφωνο Μέλους";

                    membersInner.appendChild(memberName);
                    membersInner.appendChild(memberEmail);
                    membersInner.appendChild(memberPhone);
                    memberDiv.appendChild(membersInner);
                }
                memberFields.appendChild(memberDiv);
            }
        }
    }
}

function popModal(id) {
    const modal = new Modal();
    modal.popModal(id);
}

function closeModal(id) {
    const modal = new Modal();
    modal.closeModal(id);
}

function createButtonDeleteClickHandler(button, team) {
    const loading = document.getElementById("loading-circle");

    return function () {
        const confirmDelete = confirm("Είστε σίγουρος ότι θέλετε να διαγράψετε την ομάδα;");
        if (confirmDelete) {
            button.disabled = true;
            loading.style.display="flex";
            const dataJson = {};
            dataJson.group_name=team.group_name;    
            const jsonString = JSON.stringify(dataJson, null, 2);
            const xhr = new XMLHttpRequest();
            const url = ip + "/game/controlers/deletePost.php";
            xhr.open("POST", url, true);
        
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    button.disabled = false;
                    loading.style.display="none";
                    refresh();
                }
            };
        
            xhr.send(jsonString);
        }
    
    };
}

function createCheckInClickHandler(deleteButton,editButton,checkInChecKBox, team) {

    return function () {
        deleteButton.disabled=true;
        editButton.disabled=true;
        checkInChecKBox.disabled = true;
        loading.style.display="flex";
        var dataJson = {};
        dataJson.group_name = team.group_name;
        const jsonString = JSON.stringify(dataJson, null, 2);
        const xhr = new XMLHttpRequest();
        const url = ip + "/game/controlers/checkIn.php";
        xhr.open("POST", url, true);
    
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const json = JSON.stringify(xhr.responseText);
                loading.style.display="none";
                console.log(json);
            }
        };
    
        xhr.send(jsonString);
    };
}
let teamOnEdit="";
function createButtonEditClickHandler(button, team) {
    const modal = new Modal();
    const loading = document.getElementById("loading-circle");
    return function () {
        loading.style.display="flex";
        const xhr = new XMLHttpRequest();
        const url = ip + "/game/controlers/getRouties.php";
        xhr.open("GET", url, true);
        // const json = {};
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const json = JSON.parse(xhr.responseText);
                loading.style.display="none";
                var select = document.getElementById("choices");
                select.innerHTML = "";
                var option = document.createElement("option");
                option.text = "choose an option...";
                option.value ="choose";
                option.disabled= true;
                option.selected = true;
                select.appendChild(option);

                json.forEach(function (item) {
                    var option = document.createElement("option");  
                    option.text = item.route_name; 
                    option.value = item.route_name; 
                    select.appendChild(option);
                });

                
                modal.popModal("edit-team");
                const members = team.group_members;
                const teamOnEditModalContent = document.getElementById("team-on-edit-div");
                teamOnEditModalContent.innerHTML="";
        
                members.forEach(function (member) {
                    const memberDiv = document.createElement('div');
                    memberDiv.id="delete-member-div";
                    const deleteButton = document.createElement('button');
                    deleteButton.style.backgroundColor="#F44336";
                    const deleteIcon = document.createElement('img');
                    deleteIcon.className="icon";
                    deleteIcon.src="icons/delete.png";
                    deleteButton.appendChild(deleteIcon);
                    const deleteText = document.createTextNode("");
                    // deleteButton.appendChild(deleteText);
                    // deleteButton.addEventListener('click', deleteTeamMember(member_name));
                    deleteButton.className="button";
        
                    const memberP = document.createElement('p');
                    memberP.innerHTML = member.member_name.toString();
                    const memberPPhone = document.createElement('p');
                    memberPPhone.innerHTML = member.member_phone.toString();
                    memberDiv.appendChild(memberP);
                    memberDiv.appendChild(memberPPhone);
                    // memberDiv.appendChild(deleteButton);
        
                    teamOnEditModalContent.appendChild(memberDiv);
                });
            }
        };
    
        xhr.send();
        teamOnEdit=team.group_name;
    }
}



function groupUpdate() {
    
    loading.style.display="flex";
    var select = document.getElementById("choices");
    var selectedValue = select.value;
    var dataJson = {};
    dataJson.route_name = selectedValue;
    dataJson.group_name = teamOnEdit;

    const jsonString = JSON.stringify(dataJson, null, 2);
    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/updateRouteGroup.php";
    xhr.open("POST", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            loading.style.display="none";
            closeModal("edit-team");
            refresh();
        }
    };

    xhr.send(jsonString);
    
}

function loadData(getJson) {
    const tableBody = document.getElementById('table-data-teams').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = "";
    for (let i = 0; i < getJson.length; i++) {
        let team = getJson[i];
        let previousPoint = team.previous_point_name;
        const newRow = tableBody.insertRow();
        const teamName = newRow.insertCell(0);
        const teamRoute = newRow.insertCell(1);
        
        const buttonCell = newRow.insertCell(2);
        const checkIn = newRow.insertCell(3);
        checkIn.style.width="50px";
        var checkInChecKBox = document.createElement("input");
        checkInChecKBox.type="checkbox";
        checkInChecKBox.className="checkin";
        const deleteButton = document.createElement('button');
        const editButton = document.createElement('button');
        deleteButton.id="delete-button";
        editButton.id="edit-button";
        deleteButton.className = 'button';
        editButton.className = 'button';

        if (team.check_in === 1) {
            deleteButton.disabled=true;
            editButton.disabled=true;
            checkInChecKBox.checked = true;
            checkInChecKBox.disabled = true;
        }

        checkInChecKBox.addEventListener('change', createCheckInClickHandler(deleteButton,editButton,checkInChecKBox, team));
        checkIn.appendChild(checkInChecKBox);

        //adds all the text fields to the table
        var teamNameText = document.createTextNode(team.group_name);
        teamName.appendChild(teamNameText);
        groupNames.push(team.group_name);

        let group_route = team.group_route;

        group_route.forEach(function (point, index){

            const routePointSpan = document.createElement('span');
            routePointSpan.innerHTML = point.toString();
            
            if (point == previousPoint){
                routePointSpan.style.background="#4CAF50";
            }
            if (index !== group_route.length-1) {
                routePointSpan.innerHTML +=" > ";
            }

            teamRoute.appendChild(routePointSpan);
        });

        const editIcon = document.createElement('img');
        const deleteIcon = document.createElement('img');
        const editText = document.createTextNode("");
        const deleteText = document.createTextNode("");
        const div = document.createElement('div');
        div.id="button-wrap-div"



        editIcon.className="icon";
        editIcon.src="icons/edit.png";

        deleteIcon.className="icon";
        deleteIcon.src="icons/delete.png";

        editButton.appendChild(editIcon);
        deleteButton.appendChild(deleteIcon);
        editButton.appendChild(editText);
        deleteButton.appendChild(deleteText);

        div.appendChild(editButton);
        div.appendChild(deleteButton);

        deleteButton.addEventListener('click', createButtonDeleteClickHandler(deleteButton, team));
        editButton.addEventListener('click', createButtonEditClickHandler(deleteButton, team));

        buttonCell.appendChild(div);

    }
}

function createSubmitGroupRouteButtonClickHandler(submitGroupRouteButton, team) {
    return function () {
        loading.style.display="flex";
        var select = document.getElementById("choices");
        var selectedValue = select.value;
        var dataJson = {};
        dataJson.route_name = selectedValue;
        dataJson.group_name = team.group_name;
    
        const jsonString = JSON.stringify(dataJson, null, 2);
        const xhr = new XMLHttpRequest();
        const url = ip + "/game/controlers/updateRouteGroup.php";
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

}

