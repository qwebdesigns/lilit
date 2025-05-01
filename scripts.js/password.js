const b1 = document.getElementById('body1');
const b2 = document.getElementById("body2");

b2.classList.add("hidden");
var pass = "c3VycGFzczE=";
var local_pass = localStorage.getItem('password');
var _pass = localStorage.getItem('local_password');


if(!_pass){
    localStorage.setItem("local_password", '0');
}


if (!local_pass) {
    localStorage.setItem('password', pass);
} else {
    if (local_pass != pass) {
        localStorage.setItem("password", pass);
    }
    else{
        if (local_pass == _pass && pass == _pass) {
            b1.classList.add("hidden");
            b2.classList.remove("hidden");
        }
    }
}

//password_area
var hash = btoa(pass);
console.log(local_pass);
function chek_pass() {
    var form_password1 = document.getElementById("password_area").value;
    var form_password = btoa(form_password1);
    if (local_pass == form_password && pass == form_password) {
        b1.classList.add("hidden");
        b2.classList.remove("hidden");
        localStorage.setItem("local_password", form_password);
    }
}