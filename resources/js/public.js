import "./bootstrap";
window.Echo.channel("user-login").listen("UserLogin", (e) => {
    console.log(e);
    alert("User logged in");
});
