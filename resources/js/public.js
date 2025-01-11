import "./bootstrap";
console.log("hello vite");
window.Echo.channel("user-login").listen("UserLogin", (e) => {
    console.log(e);
    alert("User logged in");
});
