$(document).ready(function() {
    let dropdowns = document.querySelectorAll('.dropdown-toggle2');

    dropdowns.forEach((dd)=>{
        dd.addEventListener('mouseover', function (e) {
            var el = this.nextElementSibling;
            el.style.display = el.style.display==='block'?'none':'block';
        })

        dd.addEventListener('mouseout', function (e) {
            var el = this.nextElementSibling;
            el.style.display = el.style.display==='block'?'block':'none';
        })
    })
})