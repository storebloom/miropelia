"use strict";
const wpThemeURL = "orbemorder.local";
const pageFile = 1;
const pageTitle = "";

// forEach method, could be shipped as part of an Object Literal/Module
const forEach = function (array, callback, scope) {
    for (let i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]); // passes back stuff we need
    }
};

document.addEventListener("DOMContentLoaded", function(){

    // Explore page functions.
    const explorePage = document.querySelector('.page-template-explore');
    if (miroElExists(explorePage)) {
        // Add character select functionality.
        const characters = document.querySelectorAll('.character-item');

        forEach(characters, function (index, value) {
            value.addEventListener( 'click', function (e) {
                const characterChoice = e.target;
                const imgSrc = characterChoice.getAttribute('src');

                if (miroElExists(document.querySelector('.character-item img.engage'))) {
                    removeMiroClass( document.querySelector( '.character-item img.engage' ), 'engage' );
                }

                addMiroClass(characterChoice, 'engage');
                addMiroClass(document.getElementById('engage-explore'), 'engage');

                document.getElementById('map-character-icon').setAttribute('src', imgSrc);
            } );
        });

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
        addMiroClass(marketSign, 'engage');
    }

    // Add roll in for market cart.
    const marketCart1 = document.querySelector('.market-cart-1');
    if (miroElExists(marketCart1)) {
        addMiroClass(marketCart1, 'engage');
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

            addMiroClass( loginModal, 'engage' );

            document.querySelector( '.close-login' ).addEventListener( 'click', function () {
                removeMiroClass( loginModal, 'engage' );
            } );
        } );
    }

    // Click for sub nav.
    document.querySelectorAll("#menu-main .menu-item-has-children").forEach( item => {
        item.addEventListener('click', function ( event ) {
            const menuItem = event.target;

            toggleMiroClass( menuItem, 'engage' );

            // Click out of sub nav.
            document.addEventListener( 'click', function ( event ) {
                const isClickInside = menuItem.contains( event.target );

                if ( !isClickInside ) {
                    removeMiroClass( menuItem, 'engage' );
                }
            } );
        }, false );
    });

    // Add intermittent spin for main logo and blue glow.
    setInterval(
        function() {
            const logo = document.querySelector('.logo .logo-icon');

            spinMiroLogo(logo, 'engage');

            // Logo glow.
            const logoCont = document.querySelector('.logo');

            spinMiroLogo(logoCont, 'engage');
        },
        30000
    );

    // Run logo spin and blue glow shortly after page load once.
    setTimeout(
        function() {
            const logo = document.querySelector('.logo .logo-icon');

            spinMiroLogo(logo, 'engage');

            // Logo glow.
            const logoCont = document.querySelector('.logo');

            spinMiroLogo(logoCont, 'engage');
        },
        3000
    );
});


/**
 * Helper function for logo spin and adds/removes class shortly.
 *
 * @param element
 * @param elementArr
 * @param name
 */
function spinMiroLogo(element,name) {
    addMiroClass(element, name);
    setTimeout(
        function() {
            removeMiroClass(element, name);
        },
        1000
    );
}

/**
 * Helper function to add class to element.
 *
 * @param element
 * @param name
 */
function addMiroClass(element, name) {
    if (element.className.split(' ').indexOf(name) === -1) {
        element.className += " " + name;
    }
}

/**
 * Helper class that adds/removes class if class exists/doesn't exist.
 *
 * @param element
 * @param elementArr
 * @param name
 */
function toggleMiroClass(element,name) {
    if (element.className.split(' ').indexOf(name) === -1) {
        element.className += " " + name;
    } else {
        element.classList.remove(name);
    }
}

/**
 * Helper function to remove class from element.
 *
 * @param element
 * @param name
 */
function removeMiroClass(element, name) {
    element.classList.remove(name);
}


/**
 * Helper function to check if element exists.
 *
 * @param element
 * @returns {boolean}
 */
function miroElExists(element) {
   return document.body.contains(element);
}

/**
 * Creates a user or adds error to page.
 *
 * @param username
 * @param email
 * @param password
 */
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

/**
 * Helper function to determine if element has class.
 * @param element
 * @param selector
 * @returns {boolean}
 */
function miroHasClass(element,selector) {
    return element.classList.contains(selector);
}

/**
 * Engages the explore page game functions.
 */
function engageExploreGame() {
    const d = {};
    const x = 3;
    const box = document.getElementById('map-character');
    const touchButtons = document.querySelector('.touch-buttons');

    // Show leave map link and keys guide.
    document.getElementById('leave-map').style.opacity = '1';

    // Flash key-guide.
    const keyGuide = document.getElementById('key-guide');
    spinMiroLogo(keyGuide, 'engage');

    // Bring touch buttons forward and flash arrows.
    document.querySelector('.touch-buttons').style.zIndex = '99';
    spinMiroLogo(touchButtons, 'engage');

    // Run arrow flash intermittently.
    const buttonShow = setInterval(function() {
        spinMiroLogo(touchButtons, 'engage');
        spinMiroLogo(keyGuide, 'engage');
    }, 10000);

    // Add listeners for explore keyboard movement.
    document.addEventListener('keydown', function(e) {
        e.preventDefault();
        d[e.which] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    } );
    document.addEventListener('keyup', function(e) {
        e.preventDefault();
        d[e.which] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    } );


    // Add listener for touch events.
    document.querySelector('.top-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;
        d[38] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.top-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;
        d[38] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.top-middle').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[38] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.top-middle').addEventListener('touchend', function(e){
        e.preventDefault();
        d[38] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.top-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[38] = true;
        d[39] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.top-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[38] = false;
        d[39] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[39] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.middle-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[39] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-left').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[37] = true;
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-left').addEventListener('touchend', function(e){
        e.preventDefault();
        d[37] = false;
        d[40] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-middle').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-middle').addEventListener('touchend', function(e){
        e.preventDefault();
        d[40] = false;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-right').addEventListener('touchstart', function(e){
        e.preventDefault();
        d[39] = true;
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval(buttonShow);
    });
    document.querySelector('.bottom-right').addEventListener('touchend', function(e){
        e.preventDefault();
        d[39] = false;
        d[40] = false;

        // Turn off arrow flash if character moved.
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


    /**
     * Helper function that returns position of element.
     *
     * @param v
     * @param a
     * @param b
     * @returns {number}
     */
    function miroExplorePosition(v,a,b) {
        const pane = document.querySelector( '.container' );
        const box = document.querySelector( '#map-character img' );
        const modal = document.querySelectorAll('.map-item');

        // Overlap check for map item.
        forEach(modal, function(index, value) {
            if ( elementsOverlap( box.getBoundingClientRect(), value.getBoundingClientRect() ) ) {
                if (!miroHasClass(value, 'engage')) {
                    addMiroClass( value, 'engage' );
                }
            } else {
                if (miroHasClass(value, 'engage')) {
                    removeMiroClass( value, 'engage' );
                }
            }
        });

        const w = pane.offsetWidth - box.offsetWidth;
        const n = parseInt(v, 10) - (d[a] ? x : 0) + (d[b] ? x : 0);

        return n < 0 ? 0 : n > w ? w : n;
    }

    /**
     * Check if elements are touching.
     *
     * @param rect1
     * @param rect2
     * @returns {boolean}
     */
    function elementsOverlap(rect1, rect2) {
        return !(rect1.right < rect2.left ||
                        rect1.left > rect2.right ||
                        rect1.bottom < rect2.top ||
                        rect1.top > rect2.bottom);
    }
}
