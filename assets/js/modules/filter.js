import {Flipper,
    spring
} from 'flip-toolkit' // https://github.com/aholachek/react-flip-toolkit

/**
 * @property {HTMLFormElement} form
 * @property {HTMLElement} products
 * @property {HTMLElement} pagination
 */
export default class Filter {

    /** @param {HTMLElement|null} element */
    constructor(element) {
        if (element === null) {
            return
        }
        // Création de variables qui ciblent les différents classes js-filter-...
        this.form = element.querySelector('.js-filter-form')
        this.products = element.querySelector('.js-filter-products')
        this.pagination = element.querySelector('.js-filter-pagination')
        this.bindEvents()
    }

    /**
     * Ajouter des comportements aux différents éléments
     */
    bindEvents() {
        // input => le champ de recherche + input[type=checkbox] des catégories et des emplacements
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        })
           this.pagination.addEventListener('click', e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }) 
        }

      async loadForm() {
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams()

        data.forEach((value, key) => {
            params.append(key, value)
        })
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url, append = false) {
        this.showLoader() // Charger la fonction qui affiche le loader
        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax', 1)
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.flipProducts(data.products, append) // Chargement de la fonction qui anime le placement des produits
                this.pagination.innerHTML = data.pagination
            params.delete('ajax') // Supprimer le mot 'ajax' de l'url
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString()) // Remplace l'élément courant dans l'historique de l'utilisateur et changera l'url afichée dans la barre de d'adresse
        } else {
            console.error(response) // Afficher l'erreur dans la console
        }
        this.hideLoader() // Charger la fonction qui cache le loader
    }

    /**
     * Remplace les produits avec un effet d'animation flip
     * @param {string} products 
     */
    flipProducts(products, append) {
        const springPreset = 'gentle' // https://codepen.io/aholachek/pen/bKmZbV
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({
                    translateY,
                    opacity
                }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        }
        const appearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({
                    translateY,
                    opacity
                }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 20
            })
        }
        const flipper = new Flipper({
            element: this.products
        })
        this.products.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springPreset,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            })
        })
        flipper.recordBeforeUpdate()
        if (append) {
            this.products.innerHTML += products
        } else {
            this.products.innerHTML = products
        }
        this.products.innerHTML = products
        this.products.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springPreset,
                flipId: element.id,
                onAppear: appearSpring
            })
        })
        flipper.update()
    }

    /**
     * Afficher le loader
     */
    showLoader() {
        this.form.classList.add('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'false')
        loader.style.display = null
    }

    /**
     * Cacher le loader
     */
    hideLoader() {
        this.form.classList.remove('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'true')
        loader.style.display = 'none'
    }
}