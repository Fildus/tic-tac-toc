/**
 * Open and close the main header
 */
document.querySelector('#main-nav-button').addEventListener("click", function () {
    let nav = document.querySelector('#main-nav-items')

    if (nav.classList.contains("transition-hidden")) {
        nav.classList.replace("transition-hidden", "transition-show")
    } else {
        nav.classList.replace("transition-show", "transition-hidden")
    }
})

/**
 * Open and close the connexion menu
 */
let mainNavConnexionPopup = document.querySelector('#main-nav-connexion-popup')
mainNavConnexionPopup.addEventListener("click", function (e) {
    e.stopPropagation()
    e.stopImmediatePropagation()
})
let mainNavConnexionButton = document.querySelector('#main-nav-connexion-button')
mainNavConnexionButton.addEventListener("click", function (e) {
    e.stopPropagation()
    e.stopImmediatePropagation()
    if (mainNavConnexionPopup.classList.contains("hidden")) {
        mainNavConnexionPopup.classList.replace("hidden", "no_css-show")
    } else {
        mainNavConnexionPopup.classList.replace("no_css-show", "hidden")
    }
})

document.body.addEventListener("click", function () {
    if (mainNavConnexionPopup.classList.contains("no_css-show")) {
        mainNavConnexionPopup.classList.replace("no_css-show", "hidden")
    }
})
