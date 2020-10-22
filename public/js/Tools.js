"use strict"

/**
 * Class static de fonctions utiles
 */
class Tools {
    /**
     * 
     * @param {string} className "primary, info, warning ou danger"
     * @param {string} htmlLink "lien vers la page"
     * @param {string} toolTipText texte à afficher dans le tooltip
     * @param {string} htmlTag tag html
     */
    static createLink(className = null, htmlLink, textContent = null, toolTipText = null, htmlTag = null) {
        let size = screen.width >= 360 ? 'fa-2x' : 'fa-1x';
        let link = document.createElement('a');
        if (className)
            link.classList.add('btn', `btn-outline-${className}`, 'm-1', 'p-1');
        link.href = htmlLink;
        link.textContent = textContent;
        if (toolTipText) {
            link.setAttribute('data-toggle', "tooltip");
            link.setAttribute('data-placement', "top");
            link.setAttribute('title', `${toolTipText}`);
            link.innerHTML = `<i class='${htmlTag} ${size}'></i>`;
        }
        return link;
    }
}