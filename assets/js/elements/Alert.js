import {slideToTop} from '../modules/animation'

export class Alert extends HTMLElement {
    constructor ({type, message} = {}) {
        super()
        if (type !== undefined) {
          this.type = type
        } else {
          this.type = this.getAttribute('type')
        }
        if (!message) {
          message = this.innerHTML
        }
        if (this.type === 'error' || this.type === null) {
          this.type = 'danger'
        }
        this.message = message
        this.close = this.close.bind(this)
      }

      connectedCallback () {
        const duration = this.getAttribute('duration')
        let progressBar = '';
        if (duration !== null) {
          progressBar = `<div class="alert__progress" style="animation-duration: ${duration}">`
          window.setTimeout(this.close, duration * 1000)
        }
        this.innerHTML = `<div class="alert alert-${this.type}">
            <svg class="icon icon-{$name}">
              <use xlink:href="/sprite.svg#${this.icon}"></use>
            </svg>
            ${this.message}
            <button class="alert-close">
              <svg class="icon">
                <use xlink:href="/sprite.svg#cross"></use>
              </svg>
            </button>
            ${progressBar}
          </div>`
        this.querySelector('.alert-close').addEventListener('click', (e) => {
          e.preventDefault()
          this.close()
        })
      }

      close () {
        const element = this.querySelector('.alert')
        element.classList.add('out')
        window.setTimeout(async () => {
          await slideToTop(element)
          this.parentElement.removeChild(this)
          this.dispatchEvent(new CustomEvent('close'))
        }, 500)
      }

    get icon() {
        if (this.type === 'danger') {
            return 'warning'
        } else if (this.type === 'success') {
            return 'check'
        }
    }
}

export class FloatAlert extends Alert {
    constructor (options = {}) {
        super(options)
        this.classList.add('is-floating')
        this.style.position = 'fixed'
        this.style.top = '20px'
        this.style.right = '20px'
        this.style.maxWidth = '400px'
        this.style.zIndex = '100'
    }
}

customElements.define('alert-message', Alert)
customElements.define('alert-floating', FloatAlert)