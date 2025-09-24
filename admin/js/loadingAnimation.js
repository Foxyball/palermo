
    function showLoading(e) {
        var div = document.createElement('div');
        div.innerHTML = "Loading...<br />";
        div.style.cssText = 'position: fixed; top: 5%; left: 40%; z-index: 5000; width: 422px; text-align: center; background: #EDDBB0; border: 1px solid #000';
        document.body.appendChild(div);
        return true;
        // These 2 lines cancel form submission, so only use if needed.
        //window.event.cancelBubble = true;
        //e.stopPropagation();
    }
