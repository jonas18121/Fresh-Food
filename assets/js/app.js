// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

import './elements/Alert'
import './elements/Burger'
import './elements/DatePicker'
import './elements/UserSelect'
import Filter from './modules/filter'
import './elements/tableResponsive'

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

new Filter(document.querySelector('.js-filter'))