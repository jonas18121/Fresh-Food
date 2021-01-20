export function debounce (func, wait, immediate) {
    let timeout
    return function(){
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            timeout = null
            if(!immediate) func.apply(context, args);
        }, wait)
        if(!immediate && !timeout) func.apply(context, args); 
    };
}