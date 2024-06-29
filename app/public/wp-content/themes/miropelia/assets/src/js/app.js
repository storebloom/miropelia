"use strict";
const pageFile = 1;
const pageTitle = "";

// forEach method, could be shipped as part of an Object Literal/Module
const forEach = function (array, callback, scope) {
    for (let i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]); // passes back stuff we need
    }
};

document.addEventListener("DOMContentLoaded", function(){
    // Loop through all youtube thumbnails on page.
    const youtubeThumbs = document.querySelectorAll( '.youtube-thumb' );

    // If thumbs exist loop through.
    if ( youtubeThumbs ) {
        youtubeThumbs.forEach( youtubeThumb => {
            // Get youtube url from alt.
            const youtubeUrl = youtubeThumb.querySelector( 'img' ).getAttribute( 'alt' );

            // If url exists, create a click event for the thumbnail to load the new player.
            if ( youtubeUrl && '' !== youtubeUrl ) {
                youtubeThumb.addEventListener( 'click', () => {
                    // When thumbnail is clicked, create a new iframe element and set URL to the youtube video.
                    const youtubeplayer = document.createElement( 'iframe' );
                    // Set iframe path to youtube url. Add autoplay to url.
                    youtubeplayer.src = youtubeUrl + '&autoplay=1&rel=0';

                    // Set iframe attributes.
                    youtubeplayer.setAttribute( 'allowfullscreen', '' );
                    youtubeplayer.setAttribute( 'allow', 'accelerometer; autoplay; clipboard-write; encrypted-media, gyroscope; picture-in-picture' );
                    youtubeplayer.setAttribute( 'frameborder', '0' );

                    // Replace image with iframe.
                    youtubeThumb.after( youtubeplayer );
                    // Remove image.
                    youtubeThumb.remove();
                } );
            }
        } );
    }

    // Add click event for gallery block.
    const blockItem = document.querySelectorAll('.blocks-gallery-item');
    if (miroElExists(blockItem[0])) {
        forEach(blockItem, function(index, value) {
           value.addEventListener('click', function(e) {
               const imageSelected = e.currentTarget;
               addMiroClass(imageSelected, 'engage');

            // Click out of sub nav.
            document.addEventListener( 'click', function ( event ) {
                const isClickInside = e.target.contains( event.target );

                if ( !isClickInside ) {
                    removeMiroClass( imageSelected, 'engage' );
                }
            });
           });
        });
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
            const twoChapters = document.querySelector('input[name="two_chapters"]').value;

            createMiroUser(username, email, password, twoChapters);
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

    // Mobile nav click.
    const mobileToggle = document.querySelector( '.menu-toggle' );

    if ( mobileToggle ) {
        mobileToggle.addEventListener( 'click', () => {
            const mainMenu = document.querySelector( '.menu-main-container' );

            mobileToggle.classList.toggle( 'engage' );

            if ( mainMenu ) {
                mainMenu.classList.toggle( 'engage' );
            }
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
 * Helper function to insert element before another.
 * @param firstEl
 * @param insertEl
 */
function miroInsertBefore(firstEl, insertEl) {
    firstEl.parentNode.insertBefore( insertEl, firstEl.nextSibling);
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
 * @param twoChapters
 */
function createMiroUser(username, email, password, twoChapters) {
    const wpThemeURL = siteUrl.replace('https://', '').replace('http://', '').replace('www', '');
    const filehref = "https://" + wpThemeURL + "/wp-json/wp/v2/users";
    const restApiKey = 'aG9tb25pYW46QnVyYmFuazQ1MjQzIQ==';

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

        const successSignup = 'yes' === twoChapters ? '/thank-you-for-signing-up-enjoy-the-first-two-chapters-or-orbem/?theyearnedit=true&loginuser=' + username : '/?loginuser=' + username;

        xhr.addEventListener( "load", function (e) {
            if (undefined !== JSON.parse(e.currentTarget.response).id) {
                window.location.href = successSignup;
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
