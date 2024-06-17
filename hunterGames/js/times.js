
const ip = myApp.ip; 
let loading = document.getElementById("loading-circle");
refresh();

function refresh() {
    loading.style.display="flex";
    const xhr = new XMLHttpRequest();
    const url = ip+"/game/controlers/getTimeForEachTeams.php";
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


function loadData(json) {
    
    json.groups.forEach(group => {
        if (group.group_status === "big") {
            const times = group.group_name.times;
            let rectDiv = document.createElement("div");
            var teamDiv = document.getElementById("big");
            rectDiv.className="rectangle";
            rectDiv.addEventListener('click', rectCickListener(rectDiv));
            var text = document.createTextNode(group.group_name);
            rectDiv.appendChild(text);
            let cardDiv = document.createElement("div");
            cardDiv.className="card";

            group.times.forEach(point => {
                var text = document.createElement("p");
                text.innerHTML=point.point + ": " + point.time;
                cardDiv.appendChild(text);
            });

            rectDiv.appendChild(cardDiv);
            teamDiv.appendChild(rectDiv);
        }

        else {
            const times = group.group_name.times;
            let rectDiv = document.createElement("div");
            var teamDiv = document.getElementById("small");
            rectDiv.className="rectangle";
            rectDiv.addEventListener('click', rectCickListener(rectDiv));
            var text = document.createTextNode(group.group_name);
            rectDiv.appendChild(text);
            let cardDiv = document.createElement("div");
            cardDiv.className="card";

            group.times.forEach(point => {
                var text = document.createElement("p");
                text.innerHTML=point.point + ": " + point.time;
                cardDiv.appendChild(text);
            });

            rectDiv.appendChild(cardDiv);
            teamDiv.appendChild(rectDiv);
        }
    });

}

function rectCickListener(rectangle) {
    return function() {
      var card = rectangle.querySelector('.card');
      var isExpanded = card.classList.contains('expanded');
      if (!isExpanded) {
        card.classList.add('expanded');
        card.style.maxHeight = card.scrollHeight + "px";
      } else {
        card.style.maxHeight = "0px";
        card.addEventListener('transitionend', function() {
          card.classList.remove('expanded');
        }, {once: true});
      }
    }
}
function start(status) {
    const confirmStart = confirm("Είστε σίγουροι ότι θέλετε να ξεκινήσετε τον χρόνο;");
    if (confirmStart) {
        loading.style.display="flex";
        var dataJson = {};
        dataJson.group_status = status;

        const jsonString = JSON.stringify(dataJson, null, 2);
        const xhr = new XMLHttpRequest();
        const url = ip + "/game/controlers/start_time.php";
        xhr.open("POST", url, true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                loading.style.display="none";
            }
        };

        xhr.send(jsonString);
    }
    
}

function stop(status){

    const confirmStop = confirm("Είστε σίγουροι ότι θέλετε να σταματήσετε τον χρόνο;");
    if (confirmStop) {
        loading.style.display="flex";
        var dataJson = {};
        dataJson.group_status = status;
    
        const jsonString = JSON.stringify(dataJson, null, 2);
        const xhr = new XMLHttpRequest();
        const url = ip + "/game/controlers/stop_time.php";
        xhr.open("POST", url, true);
    
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                loading.style.display="none";
            }
        };
        
        xhr.send(jsonString);
    }

}

function duration(status){
    loading.style.display="flex";
    const xhr = new XMLHttpRequest();
    const url = ip + "/game/controlers/duration.php?status="+status;
    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            loading.style.display="none";
            const json = JSON.parse(xhr.responseText);
            var p = document.getElementById("duration-"+status)
            p.innerHTML=json.duration;
        }
    };

    xhr.send();
}

