document.addEventListener("DOMContentLoaded",()=>{let e=document.querySelector(".header"),t=document.querySelector(".nav-toggle"),r=document.querySelector(".nav-sidebar"),s=document.querySelectorAll(".has-dropdown"),l=document.querySelectorAll(".nav-link"),c,a=()=>{window.scrollY>50?e.classList.add("sticky"):e.classList.remove("sticky")};window.addEventListener("scroll",()=>{c||(c=setTimeout(()=>{a(),c=null},50))});let i=()=>{r.classList.toggle("active"),t.classList.toggle("active")};t.addEventListener("click",i),s.forEach(e=>{let s=e.querySelector("a"),c=e.querySelector(".dropdown");s.addEventListener("click",t=>{if(window.innerWidth<=768){t.preventDefault();let r=e.classList.toggle("active");c.style.display=r?"block":"none"}});let a=c.querySelectorAll("a");a.forEach(e=>{e.addEventListener("click",e=>{e.stopPropagation(),l.forEach(e=>e.classList.remove("active")),window.innerWidth<=768&&(r.classList.remove("active"),t.classList.remove("active"))})})});let o,n=()=>{window.innerWidth>768&&(r.classList.remove("active"),t.classList.remove("active"),s.forEach(e=>{e.classList.remove("active"),e.querySelector(".dropdown").style.display=""}))};window.addEventListener("resize",()=>{o||(o=setTimeout(()=>{n(),o=null},200))}),document.addEventListener("click",e=>{e.target.closest(".has-dropdown")||s.forEach(e=>{e.classList.remove("active"),window.innerWidth<=768&&(e.querySelector(".dropdown").style.display="none")})})}),document.addEventListener("DOMContentLoaded",()=>{let e=document.querySelector(".search-toggle"),t=document.getElementById("searchWindow"),r=document.querySelector(".close-search"),s=e=>{t.classList[e]("active")};e.addEventListener("click",()=>s("add")),r.addEventListener("click",()=>s("remove")),t.addEventListener("click",e=>{e.target===t&&s("remove")})});
var lazyAdsense = false;
var lazyAnalytics = false;
