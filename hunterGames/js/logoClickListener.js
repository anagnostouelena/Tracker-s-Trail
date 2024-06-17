console.log(myApp.ip); 
function backToHome() {
    var currentUrl = window.location.href;
    var urlParts = currentUrl.split("/");
    var currentPage = urlParts[urlParts.length - 1];
    var newPage = "index.html";
    var newUrl = currentUrl.replace(currentPage, newPage);
    window.location.href = newUrl;
    
   
}