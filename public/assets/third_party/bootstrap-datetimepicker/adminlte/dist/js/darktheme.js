/*
    https://dev.to/sbrevolution5/creating-a-toggleable-dark-mode-theme-ned
*/

function toggleDark() {
    let element = document.getElementById("layoutBody")    
    let dark    = localStorage.getItem("lteDarkMode")

    let buttonElem = document.getElementById("darkIcon")
    let navElement    = document.querySelector("nav")
    let sidebarElem   = document.querySelector(".main-sidebar")

    element.classList.toggle("dark-mode")

    if (dark === 'true') {
        localStorage.setItem("lteDarkMode", 'false')
        console.log("Dark mode off")
    }
    else {
        localStorage.setItem("lteDarkMode", 'true')
        console.log("Dark mode on")
    }

    buttonElem.classList.toggle("fa-moon")
    buttonElem.classList.toggle("fas")
    buttonElem.classList.toggle("far")
    buttonElem.classList.toggle("fa-sun")
    
    navElement.classList.toggle("navbar-white")
    navElement.classList.toggle("navbar-dark")
}

function loadThemeSwitcher() {    
    let mode = localStorage.getItem("lteDarkMode")
    
    if (mode === null) {
        localStorage.setItem("lteDarkMode", 'false')
    }

    if (mode === 'true') {
        let bodyElem   = document.getElementById("layoutBody")   
        let navElement = document.querySelector("nav")
        let buttonElem = document.getElementById("darkIcon")

        bodyElem.classList.add("dark-mode")

        navElement.classList.remove("navbar-white")
        navElement.classList.add("navbar-dark")

        buttonElem.classList.add("far")
        buttonElem.classList.add("fa-sun")
        buttonElem.classList.remove("fa-moon")
        buttonElem.classList.remove("fas")
    }
}

function resetThemeMode(){
    localStorage.removeItem("lteDarkMode")
}

window.addEventListener('load', () => {
    document.querySelector('[data-widget="switch-theme-mode"]').addEventListener('click', (e) => {
        e.stopImmediatePropagation()
        e.preventDefault()

        toggleDark()
    }, true);
 
    loadThemeSwitcher()
});