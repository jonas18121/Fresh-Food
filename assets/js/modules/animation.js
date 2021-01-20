/**
 * @param {HTMLElement} element 
 * @param {Number} duration 
 * @returns {Promise<boolean>}
 */
export function slideToTop (element, duration = 500) {
    return new Promise(function (resolve, reject) {
        element.style.height = element.offsetHeight + 'px'
        element.style.transitionProperty = 'height, margin, padding'
        element.style.transitionDuration = duration + 'ms'
        element.offsetHeight
        element.style.overflow = 'hidden'
        element.style.height = 0
        element.style.paddingTop = 0
        element.style.paddingBottom = 0
        element.style.marginTop = 0
        element.style.marginBottom = 0
        window.setTimeout(function() {
            element.style.display = 'none'
            element.style.removeProperty('height')
            element.style.removeProperty('padding-top')
            element.style.removeProperty('padding-bottom')
            element.style.removeProperty('margin-top')
            element.style.removeProperty('margin-bottom')
            element.style.removeProperty('overflow')
            element.style.removeProperty('transition-duration')
            element.style.removeProperty('transition-property')
            resolve(false)
        }, duration)
    })
}