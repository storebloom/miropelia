"use strict";
const wpThemeURL = "orbemorder.local";
const pageFile = 1;
const pageTitle = "";

document.addEventListener("DOMContentLoaded", function(){
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

function getPageHTTPObject() {
    var xhr = false;
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(e) {
                xhr = false;
            }
        }
    }
    return xhr;
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

        xhr.addEventListener( "load", function () {
            //window.location.href = '/?loginuser=' + username;
        } );
    }
}
