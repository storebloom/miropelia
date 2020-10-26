"use strict";
const wpThemeURL = "orbemorder.local";
const pageFile = 1;
const pageTitle = "";

document.addEventListener("DOMContentLoaded", function(){

    // Explore page functions.
    const explorePage = document.querySelector('.page-template-explore');
    if (miroElExists(explorePage)) {
        const body = document.body;
        const scrollY = body.style.top;
        body.style.position = 'fixed';

        document.getElementById( 'engage-explore' ).addEventListener( 'click', function () {
            engageExploreGame();

            document.querySelector('.explore-overlay').remove();
            body.style.position = 'unset';
        } );
    }

    // Add swing in effect to market sign.
    const marketSign = document.querySelector('.market-sign');
    if (miroElExists(marketSign)) {
        const marketSignArr = marketSign.className.split(" ");

        addMiroClass(marketSign, marketSignArr, 'engage');
    }

    // Add roll in for market cart.
    const marketCart1 = document.querySelector('.market-cart-1');
    if (miroElExists(marketCart1)) {
        const marketCart1Arr = marketCart1.className.split(" ");

        addMiroClass(marketCart1, marketCart1Arr, 'engage');
    }

    // Add register account click event.
    const registerButton = document.getElementById('register-submit');
    if (miroElExists(registerButton)) {
        registerButton.addEventListener( 'click', function () {
            const username = document.getElementById( 'user_name' ).value;
            const email = document.getElementById('user_email').value;
            const password = document.getElementById('user_password').value;

            createMiroUser(username, email, password);
        } );
    }

    // Click login button.
    const loginMiroButton = document.getElementById('login');
    if (miroElExists(loginMiroButton)) {
        loginMiroButton.addEventListener( 'click', function () {
            const loginModal = document.querySelector( '.login-modal' );
            const loginModalArr = loginModal.className.split( " " );

            addMiroClass( loginModal, loginModalArr, 'engage' );

            document.querySelector( '.close-login' ).addEventListener( 'click', function () {
                removeMiroClass( loginModal, 'engage' );
            } );
        } );
    }

    // Click for sub nav.
    document.querySelectorAll("#menu-main .menu-item-has-children").forEach( item => {
        item.addEventListener('click', function ( event ) {
            const menuItem = event.target;
            const menuItemArr = menuItem.className.split( " " );

            toggleMiroClass( menuItem, menuItemArr, 'engage' );

            // Click out of sub nav.
            document.addEventListener( 'click', function ( event ) {
                const isClickInside = menuItem.contains( event.target );

                if ( !isClickInside ) {
                    removeMiroClass( menuItem, 'engage' );
                }
            } );
        }, false );
    });

    setInterval(
        function() {
            const logo = document.querySelector('.logo .logo-icon');
            const logoArr = logo.className.split(" ");

            spinMiroLogo(logo, logoArr, 'engage');

            // Logo glow.
            const logoCont = document.querySelector('.logo');
            const logoContArr = logoCont.className.split(" ");
            spinMiroLogo(logoCont, logoContArr, 'engage');
        },
        30000
    );

    setTimeout(
        function() {
            const logo = document.querySelector('.logo .logo-icon');
            const logoArr = logo.className.split(" ");

            spinMiroLogo(logo, logoArr, 'engage');

            // Logo glow.
            const logoCont = document.querySelector('.logo');
            const logoContArr = logoCont.className.split(" ");
            spinMiroLogo(logoCont, logoContArr, 'engage');
        },
        3000
    );
});

function spinMiroLogo(element,elementArr,name) {
    addMiroClass(element, elementArr, name);

    setTimeout(
        function() {
            removeMiroClass(element, name);
        },
        1000
    );
}

function addMiroClass(element,elementArr,name) {
    if (elementArr.indexOf(name) == -1) {
        element.className += " " + name;
    }
}

function toggleMiroClass(element,elementArr,name) {
    if (elementArr.indexOf(name) == -1) {
        element.className += " " + name;
    } else {
        element.classList.remove(name);
    }
}

function removeMiroClass(element, name) {
    element.classList.remove(name);
}

function miroElExists(element) {
   return document.body.contains(element);
}

function createMiroUser(username, email, password) {
    const filehref = "https://" + wpThemeURL + "/wp-json/wp/v2/users";

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "POST", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send( JSON.stringify( {
            username: username,
            email: email,
            password: password
        } ) );

        xhr.addEventListener( "load", function (e) { console.log(e.currentTarget);
            if (undefined !== JSON.parse(e.currentTarget.response).id) {
                window.location.href = '/?loginuser=' + username;
            } else {
                document.querySelector('.error-message').textContent = JSON.parse(e.currentTarget.response).message;
            }
        } );
    }
}

function engageExploreGame() {
    const d = {};
    const x = 3;
    const box = document.getElementById('map-character');
    const touchButtons = document.querySelector('.touch-buttons');

    // Show leave map link.
    document.getElementById('leave-map').style.opacity = '1';

    // Bring touch buttons forward.
    document.querySelector('.touch-buttons').style.zIndex = '99';
    spinMiroLogo(touchButtons, touchButtons.className.split(' '), 'engage');

    const buttonShow = setInterval(function() {
        spinMiroLogo(touchButtons, touchButtons.className.split(' '), 'engage');
    }, 10000);

    // Add listeners for explore.
    document.addEventListener('keydown', function(e) {
        e.preventDefault();
        d[e.which] = true;

        clearInterval(buttonShow);
    } );
    document.addEventListener('keyup', function(e) {
        e.preventDefault();
        d[e.which] = false;
        clearInterval(buttonShow);
    } );

    // Add listener for touch buttons.
    document.querySelector('.top-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;
        d[38] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.top-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;
        d[38] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.top-middle').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[38] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.top-middle').addEventListener('touchend', function(e){
        e.preventDefault();
        d[38] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.top-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[38] = true;
        d[39] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.top-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[38] = false;
        d[39] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[39] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[39] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;
        d[40] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;
        d[40] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-middle').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[40] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-middle').addEventListener('touchend', function(e){
        e.preventDefault();
        d[40] = false;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[39] = true;
        d[40] = true;
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[39] = false;
        d[40] = false;
        clearInterval(buttonShow);
    });

    // Update explore position if on explore page.
    setInterval( function () {
        const leftVal = box.style.left;
        const topVal = box.style.top;

        box.style.left = miroExplorePosition( leftVal, 37, 39 ).toString() + 'px';
        box.style.top = miroExplorePosition( topVal, 38, 40 ).toString() + 'px';

        box.scrollIntoView();
    }, 15 );

    function miroExplorePosition(v,a,b) {
        const pane = document.querySelector( '.container' );
        const box = document.querySelector( '#map-character img' );
        const modal = document.querySelector('.map-item-modal-1');
        const modalArr = modal.className.split(' ');

        // Overlap check for map item.
        if(elementsOverlap(box.getBoundingClientRect(), document.querySelector('.map-item-1').getBoundingClientRect())) {
            if (!miroHasClass(modal, 'engage')) {
                addMiroClass( modal, modalArr, 'engage' );
            }
        } else {
            if (miroHasClass(modal, 'engage')) {
                removeMiroClass( modal, 'engage' );
            }
        }

        const w = pane.offsetWidth - box.offsetWidth;
        const n = parseInt(v, 10) - (d[a] ? x : 0) + (d[b] ? x : 0);

        return n < 0 ? 0 : n > w ? w : n;
    }

    function elementsOverlap(rect1, rect2) {
        return !(rect1.right < rect2.left ||
                        rect1.left > rect2.right ||
                        rect1.bottom < rect2.top ||
                        rect1.top > rect2.bottom);
    }

    function miroHasClass(element,selector) {
        return element.classList.contains(selector);
    }
}