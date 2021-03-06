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

    // Explore page functions.
    const explorePage = document.querySelector('.page-template-explore');
    if (miroElExists(explorePage)) {
        // Add character select functionality.
        const characters = document.querySelectorAll('.character-item');

        addMiroClass(document.querySelector('.explore-overlay'), 'engage');
        addMiroClass(document.querySelector('.greeting-message'), 'engage');

        forEach(characters, function (index, value) {
            value.addEventListener( 'click', function (e) {
                const characterChoice = e.target;
                const imgSrc = characterChoice.getAttribute('src');
                const characterName = characterChoice.getAttribute('data-character');
                const points = '' !== characterChoice.getAttribute('data-points') ?
                    characterChoice.getAttribute('data-points') :
                '0';

                addNoPoints(characterName);

                // Set points.
                document.querySelector('#explore-points span.point-amount').innerHTML = points;

                if (miroElExists(document.querySelector('.character-item img.engage'))) {
                    removeMiroClass( document.querySelector( '.character-item img.engage' ), 'engage' );
                }

                addMiroClass(characterChoice, 'engage');
                addMiroClass(document.getElementById('engage-explore'), 'engage');

                const selectedCharacter = document.getElementById('map-character-icon');

                selectedCharacter.setAttribute('src', imgSrc);
                selectedCharacter.setAttribute('data-character', characterName);
            } );
        });

        const body = document.body;
        body.style.position = 'fixed';
        const engageExplore = document.getElementById('engage-explore');

        if (miroElExists(engageExplore)) {
            engageExplore.addEventListener( 'click', function () {
                engageExploreGame();

                removeMiroClass( document.querySelector( '.explore-overlay' ), 'engage' );
                body.style.position = 'unset';
                addMiroClass(document.querySelector('.touch-buttons'), 'do-mobile');
            } );
        }

        // Add enter buttons to map items.
        const mapItems = document.querySelectorAll('.map-item');

        forEach(mapItems, function(index, value) {
            const position = value.className.replace('wp-block-group map-item ', '').replace('-map-item', '');
            const enterable = [
                'gillimank',
                'caninth-village',
                'wonderland',
                'spinebeck'
            ];

            // Add data point to button.
            if (-1 !== enterable.indexOf(position)) {
                const enterButton = document.createElement('button');

                addMiroClass(enterButton, 'enter-position');
                enterButton.setAttribute( 'data-position', position );
                enterButton.textContent = 'Enter';

                // Add button next to map-item.
                miroInsertBefore(value, enterButton);

                enterButton.addEventListener('click', function(e) {
                  const enterPosition = e.target.getAttribute('data-position');

                  enterNewArea(enterPosition);
                });
            }
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
 */
function createMiroUser(username, email, password) {
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

        xhr.addEventListener( "load", function (e) {
            if (undefined !== JSON.parse(e.currentTarget.response).id) {
                window.location.href = '/?loginuser=' + username;
            } else {
                document.querySelector('.error-message').textContent = JSON.parse(e.currentTarget.response).message;
            }
        } );
    }
}

/**
 * Adds points to user's account.
 *
 * @param amount
 * @param character
 */
function addUserPoints(amount, character, position) {
    const wpThemeURL = siteUrl.replace('https://', '').replace('http://', '').replace('www', '');
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/add-explore-points/${currentUserId}/${position}/${amount}/${character}`;
    const restApiKey = 'aG9tb25pYW46QnVyYmFuazQ1MjQzIQ==';

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();
    }
}

/**
 * Pull new area html.
 *
 * @param position
 */
function enterNewArea(position) {
    const wpThemeURL = siteUrl.replace('https://', '').replace('http://', '').replace('www', '');
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/area/${position}`;
    const restApiKey = 'aG9tb25pYW46QnVyYmFuazQ1MjQzIQ==';

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();

        xhr.addEventListener( "load", function (e) {
            const newArea = document.createElement('div');
            const defaultMap = document.querySelector('.default-map');
            const chracterItem = document.getElementById('map-character');
            const characterName = document.getElementById('map-character-icon').getAttribute('data-character');
            const returnToMap = document.getElementById('return-to-map');
            const leaveMap = document.getElementById('leave-map');

            // Hide leave map.
            removeMiroClass(leaveMap, 'engage');

            // Show return to map.
            addMiroClass(returnToMap, 'engage');

            // Set id.
            newArea.setAttribute('data-map-item-area', position);
            miroInsertBefore(defaultMap, newArea);

            // Add map items.
            newArea.innerHTML = JSON.parse(e.currentTarget.response);

            const mapContainer = document.querySelector('.container');

            addMiroClass(mapContainer, position);

            switch ( position ) {
                case 'gillimank' :
                    chracterItem.style.top = '1419px';
                    chracterItem.style.left = '1117px';
                break;
            }

            defaultMap.style.display = 'none';

            // Run no point class adder again
            addNoPoints(characterName);

            // Add click event for return to map.
            returnToMap.addEventListener('click', function(e) {
                // Remove area map items.
                document.querySelector(`div[data-map-item-area="${position}"]`).remove();

                // Remove area class.
                removeMiroClass(mapContainer, position);
                document.querySelector('.default-map').style.display = 'block';

                // Reset character position.
                chracterItem.style.top = '3600px';
                chracterItem.style.left = '1942px';

                // Show leave map.
                addMiroClass(leaveMap, 'engage');

                // Hide return to map.
                removeMiroClass(returnToMap, 'engage');
            });
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
 * Helper function to add no points class to areas that have points already.
 */
function addNoPoints(characterName) {
    const selectedCharacterPositions = undefined !== explorePoints[characterName] ? explorePoints[characterName]['positions'] : [];

    // Add no point class to positions already gotten.
    forEach(selectedCharacterPositions, function(index, value) {
        if (miroElExists(document.querySelector('.' + value))) {
            addMiroClass( document.querySelector( '.' + value ), 'no-point' );
        }
    });

    // Clear all bubbles.
    const characterSlug = document.querySelectorAll('.map-item-modal.' + characterName);
    const allBubble = document.querySelectorAll('.map-item-modal');
    forEach(allBubble, function(index, value) {
        removeMiroClass(value, 'engage');
    });

    // Show all character bubbles.
    forEach(characterSlug, function (index, value) {
        addMiroClass( value, 'engage' );
    });
}

/**
 * Engages the explore page game functions.
 */
function engageExploreGame() {
    const d = {};
    const x = 3;
    const box = document.getElementById('map-character');
    const touchButtons = document.querySelector('.touch-buttons');
    const soundControl = document.getElementById('sound-control');

    // Start music.
    playAdventureSong();

    // Show leave map link and keys guide.
    addMiroClass(document.getElementById('leave-map'), 'engage');
    document.getElementById('explore-points').style.opacity = '1';
    document.getElementById('sound-control').style.opacity = '1';

    // Flash key-guide.
    const keyGuide = document.getElementById('key-guide');
    spinMiroLogo(keyGuide, 'engage');

    // Bring touch buttons forward and flash arrows.
    spinMiroLogo(touchButtons, 'engage');

    // Run arrow flash intermittently.
    const buttonShow = setInterval(function() {
        spinMiroLogo(touchButtons, 'engage');
        spinMiroLogo(keyGuide, 'engage');
    }, 10000);

    // Add listener for sound control.
    soundControl.addEventListener('click', function() {
       const allAudio = document.querySelectorAll('audio');

       forEach( allAudio, function ( index, value ) {
           value.muted = !miroHasClass(soundControl, 'mute');
       } );

       toggleMiroClass(soundControl, 'mute');
    });

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
        const leftValInt = parseInt(leftVal, 10);
        const topValInt = parseInt(topVal, 10);

        // Kill character if they hit these positions.
        if (!miroHasClass(document.querySelector('.container'), 'gillimank') && (leftValInt >= 3632 || leftValInt <= 450 || topValInt >= 3989 || topValInt === 0) ) {
            playSplashSound();
            const overlayModalWrap = document.querySelector('.explore-overlay');

            overlayModalWrap.style.top = '0';

            addMiroClass(document.getElementById('map-character'), 'drown');
            addMiroClass(overlayModalWrap, 'engage');
            removeMiroClass(document.querySelector('.greeting-message'), 'engage');
            addMiroClass(document.querySelector('.you-died-message'), 'engage');

            setTimeout(function() {
                window.location = '/explore/';
            }, 500);
        }

        box.style.left = miroExplorePosition( leftVal, 37, 39 ).toString() + 'px';
        box.style.top = miroExplorePosition( topVal, 38, 40 ).toString() + 'px';

        box.scrollIntoView();
    }, 20 );

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
        const boxSrc = box.getAttribute('src');

        // Overlap check for map item.
        forEach(modal, function(index, value) {
            const position = value.className.replace('wp-block-group map-item ', '');
            const positionEnter = document.querySelector(
                `.enter-position[data-position="${
                    position.replace('-map-item', '').replace(' no-point', '')
                }"]`
            );

            if ( elementsOverlap( box.getBoundingClientRect(), value.getBoundingClientRect() ) ) {
                if (!miroHasClass(value, 'engage')) {
                    if (miroElExists(positionEnter)) {
                        addMiroClass( positionEnter, 'engage' );
                    }

                    addMiroClass( value, 'engage' );


                    // Play interest sound effect.
                    playInterestSound();

                    if (!miroHasClass(value, 'no-point')) {
                        const thePoints = document.querySelector('#explore-points span.point-amount');
                        let currentPoints = thePoints.innerHTML;
                        currentPoints = '' !== currentPoints ? parseInt(currentPoints, 10) : 0;
                        thePoints.innerHTML = currentPoints + 50;

                        // Add class for notification of point gain.
                        addMiroClass(thePoints, 'engage');

                        // Play sound effect for points.
                        playPointSound();

                        setTimeout(function() {
                            removeMiroClass(thePoints, 'engage');
                        }, 2000);

                        addUserPoints( '50', box.getAttribute( 'data-character' ), position );
                        addMiroClass( value, 'no-point' );
                    }
                }
            } else {
                if (miroHasClass(value, 'engage')) {
                    removeMiroClass( value, 'engage' );
                }

                if (miroElExists(positionEnter)) {
                    removeMiroClass( positionEnter, 'engage' );
                }
            }
        });

        // Engage/disengage walking class.
        if (d[37] || d[38] || d[39] || d[40]) {
            if (boxSrc.includes('png')) {
                box.setAttribute( 'src', boxSrc.replace( 'png', 'gif' ) );

                playWalkSound();
            }
        } else {
            if (boxSrc.includes('gif')) {
                box.setAttribute( 'src', boxSrc.replace( 'gif', 'png' ) );

                stopWalkSound();
            }
        }

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

    function playPointSound() {
        const character = document.getElementById('map-character');

        // Show point graphic.
        addMiroClass(character, 'point');

        setTimeout(function() {
            addMiroClass(character, 'over');

            setTimeout(function() {
                removeMiroClass(character, 'point');
                removeMiroClass(character, 'over');
            }, 500);
        }, 1000 );

        document.getElementById('ching').play();


        return false;
    }

    function playWalkSound() {
        const walkingSound = document.getElementById('walking');
        walkingSound.loop = true;
        walkingSound.volume = 0.6;
        walkingSound.play();

        return false;
    }

    function stopWalkSound() {
        const walkingSound = document.getElementById('walking');
        walkingSound.pause();
        walkingSound.currentTime = 0;

        return false;
    }

    function playSplashSound() {
        const splashSound = document.getElementById('splash');
        splashSound.play();

        return false;
    }

    function playInterestSound() {
        const interestSound = document.getElementById('interest');

        interestSound.volume = 0.5;
        interestSound.play();

        return false;
    }

    function playAdventureSong() {
        const adventureSong = document.getElementById('adventure-song');

        adventureSong.loop = true;
        adventureSong.volume = 0.2;
        adventureSong.play();
    }
}
