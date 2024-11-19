document.addEventListener("DOMContentLoaded",()=>{let e=document.querySelector(".header"),t=document.querySelector(".nav-toggle"),r=document.querySelector(".nav-sidebar"),s=document.querySelectorAll(".has-dropdown"),l=document.querySelectorAll(".nav-link"),c,a=()=>{window.scrollY>50?e.classList.add("sticky"):e.classList.remove("sticky")};window.addEventListener("scroll",()=>{c||(c=setTimeout(()=>{a(),c=null},50))});let i=()=>{r.classList.toggle("active"),t.classList.toggle("active")};t.addEventListener("click",i),s.forEach(e=>{let s=e.querySelector("a"),c=e.querySelector(".dropdown");s.addEventListener("click",t=>{if(window.innerWidth<=768){t.preventDefault();let r=e.classList.toggle("active");c.style.display=r?"block":"none"}});let a=c.querySelectorAll("a");a.forEach(e=>{e.addEventListener("click",e=>{e.stopPropagation(),l.forEach(e=>e.classList.remove("active")),window.innerWidth<=768&&(r.classList.remove("active"),t.classList.remove("active"))})})});let o,n=()=>{window.innerWidth>768&&(r.classList.remove("active"),t.classList.remove("active"),s.forEach(e=>{e.classList.remove("active"),e.querySelector(".dropdown").style.display=""}))};window.addEventListener("resize",()=>{o||(o=setTimeout(()=>{n(),o=null},200))}),document.addEventListener("click",e=>{e.target.closest(".has-dropdown")||s.forEach(e=>{e.classList.remove("active"),window.innerWidth<=768&&(e.querySelector(".dropdown").style.display="none")})})}),document.addEventListener("DOMContentLoaded",()=>{let e=document.querySelector(".search-toggle"),t=document.getElementById("searchWindow"),r=document.querySelector(".close-search"),s=e=>{t.classList[e]("active")};e.addEventListener("click",()=>s("add")),r.addEventListener("click",()=>s("remove")),t.addEventListener("click",e=>{e.target===t&&s("remove")})});
var lazyAdsense = false;
var lazyAnalytics = false;

// Function to dynamically load a script
function loadScript(src, isAsync = true, callback = null) {
    var script = document.createElement("script");
    script.src = src;
    script.async = isAsync;
    if (callback) script.onload = callback;
    document.body.appendChild(script);
}

// Event listener to load scripts lazily after scroll
window.addEventListener(
    "scroll",
    function () {
        if (
            (document.documentElement.scrollTop !== 0 || document.body.scrollTop !== 0) &&
            !lazyAdsense
        ) {
            // Load AdSense
            loadScript(
                "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2974589963821146"
            );
            lazyAdsense = true;
        }

        if (!lazyAnalytics) {
            // Load Google Analytics (GA4 and Universal Analytics)
            loadScript(
                "https://www.googletagmanager.com/gtag/js?id=G-2DF7QBZJ5T",
                true,
                initializeAnalytics
            );
            loadScript(
                "https://www.googletagmanager.com/gtag/js?id=UA-148715011-1",
                true,
                initializeAnalytics
            );
            lazyAnalytics = true;
        }
    },
    { passive: true }
);

// Function to initialize Google Analytics after scripts load
function initializeAnalytics() {
    if (!window.dataLayer) window.dataLayer = [];
    function gtag() {
        window.dataLayer.push(arguments);
    }
    gtag("js", new Date());
    gtag("config", "G-2DF7QBZJ5T"); // GA4
    gtag("config", "UA-148715011-1"); // Universal Analytics
}