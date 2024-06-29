"use strict";
let pointTimeout;

document.addEventListener("DOMContentLoaded", function(){
    // Explore page functions.
    const explorePage = document.querySelector('.page-template-explore');
    if (miroElExists(explorePage)) {
        window.history.pushState({}, document.title, window.location.pathname);

        // Engage transport function.
        engageTransportFunction();

        // Spell clicks.
        const spells = document.querySelectorAll('.spell');
        const weapon = document.getElementById( 'weapon' );

        if ( spells && weapon ) {
            spells.forEach( spell => {
                spell.addEventListener( 'click', () => {
                    const currentSpell = document.querySelector( '.spell.engage' );
                    const currentWeapon = document.querySelector( '#weapon' );
                    const theWeapon = document.querySelector( '.map-weapon' );
                    const spellType = spell.getAttribute( 'data-type' );
                    const spellName = spell.getAttribute( 'title' );
                    const spellAmount = spell.getAttribute('data-value');

                    // Remove engage from weapon.
                    currentWeapon.classList.remove('engage');

                    if ( currentSpell ) {
                        currentSpell.classList.remove( 'engage' );
                    }

                    spell.classList.add('engage');
                    theWeapon.className = 'map-weapon';
                    theWeapon.classList.add( spellType );
                    theWeapon.classList.add( spellName );
                    theWeapon.classList.add( 'spell' );
                    theWeapon.setAttribute( 'data-value', spellAmount );
                    window.weaponTime = spellAmount;
                } );
            } );

            // Use weapon instead of magic.
            weapon.addEventListener( 'click', () => {
                const currentSpell = document.querySelector( '.spell.engage' );
                const theWeapon = document.querySelector( '.map-weapon' );

                if ( currentSpell ) {
                    currentSpell.classList.remove( 'engage' );
                    theWeapon.className = 'map-weapon';
                    window.weaponTime = 200;
                }

                weapon.classList.add( 'engage' );
            } );
        }

        // Set up character choice.
        const characterChoice = document.querySelector('.character-item > img');
        if ( characterChoice ) {
            addNoPoints();
            characterChoice.classList.remove('engage');
        }

        // Set points.
        const thePoints = document.querySelectorAll( '#explore-points .point-bar' );

        if ( thePoints ) {
            thePoints.forEach( point => {
                const amount = point.getAttribute('data-amount');
                const gauge = point.querySelector('.gauge');

                if ( gauge && false === point.classList.contains( 'point-amount' ) ) {
                    point.setAttribute( 'data-amount', amount );
                    gauge.style.width = amount + 'px';
                } else {
                    const newLevel = getCurrentLevel( amount );
                    if ( levelMaps ) {
                        window.nextLevelPointAmount = JSON.parse(levelMaps)[newLevel];

                        point.setAttribute('data-amount', amount);
                        gauge.style.width = getPointsGaugeAmount(amount);
                    }
                }
            } );
        }

        document.body.style.position = 'fixed';
        const engageExplore = document.getElementById('engage-explore');

        if (miroElExists(engageExplore)) {
            engageExplore.addEventListener( 'click', function () {
                engageExploreGame();
            } );
        }

        // Reset triggered so start game.
        const primary = document.getElementById( 'primary' );
        if ( primary && true === primary.classList.contains('reset')) {
            engageExploreGame();
        }

        // Settings.
        const settingCogs = document.querySelectorAll('#settings, #storage');

        if ( settingCogs ) {
            settingCogs.forEach( settingCog => {
                if ( 'storage' === settingCog.id ) {
                    // Show item description in storage menu.
                    const menuItems = document.querySelectorAll('.retrieval-points .storage-item' );

                    if ( menuItems ) {
                        menuItems.forEach( menuItem => {
                            menuItem.addEventListener( 'click', () => {
                                showItemDescription(menuItem);
                            });
                        } );
                    }
                }

                settingCog.addEventListener('click', (e) => {
                    if ( false === e.target.classList.contains( 'close-settings') ) {
                        settingCog.classList.add( 'engage' );
                    }
                });

                settingCog.querySelector('.close-settings').addEventListener( 'click', () => {
                    const description = document.querySelector( '.retrieval-points #item-description' );
                    settingCog.classList.remove('engage');

                    if ( description ) {
                        description.innerHTML = '';
                    }
                } );
            } );
        }

        const updateSettings = document.getElementById('update-settings');

        // Save settings.
        const musicSettings = document.getElementById('music-volume');
        const sfxSettings = document.getElementById('sfx-volume');

        window.sfxVolume = sfxSettings.value / 100;

        // Volume listeners.
        musicSettings.addEventListener("input", (event) => {
            window.currentMusic.volume = event.target.value / 100;
        });

        // Volume listeners.
        sfxSettings.addEventListener("input", (event) => {
            window.sfxVolume = event.target.value / 100;
        });

        if ( updateSettings ) {
            updateSettings.addEventListener('click', () => {
                if ( musicSettings && sfxSettings ) {
                    saveSettings(musicSettings.value, sfxSettings.value);
                }
            });
        }

        // Storage menu functionality.
        // Tab logic.
        const storageTabs = document.querySelectorAll( '.menu-tabs div' );

        if ( storageTabs ) {
            storageTabs.forEach( ( storageTab, storageIndex ) => {
                storageTab.addEventListener( 'click', () => {
                    const currentTab = document.querySelector( '.menu-tabs .engage' );

                    if ( currentTab ) {
                        currentTab.classList.remove( 'engage' );
                    }

                    // Select new tab.
                    storageTab.classList.add( 'engage' );

                    const tabContent = document.querySelectorAll( '.storage-menu' );
                    const currentTabContent = document.querySelector( '.storage-menu.engage' );

                    if ( currentTabContent ) {
                        currentTabContent.classList.remove( 'engage' );
                    }

                    if ( tabContent ) {
                        tabContent[storageIndex].classList.add( 'engage' );
                    }
                } );
            } );
        }
    }

    // New game reset.
    const newGame = document.getElementById( 'new-explore' );

    if ( newGame ) {
        newGame.addEventListener('click', async () => {
            confirm( 'Are you sure you want to start a new game? All your previously saved data will be lost.' );
            await resetExplore();

            setTimeout(() => {
                window.location.href = '/explore?rst=true';
            }, 1000);
        });
    }

    // Show/hide create or login forms
    const createAccount = document.getElementById( 'create-account' );
    const loginAccount = document.getElementById( 'login-account' );

    if ( createAccount && loginAccount ) {
        const login = document.querySelector( '.login-form' );
        const register = document.querySelector( '.register-form' );

        createAccount.addEventListener( 'click', () => {
            if ( login && register ) {
                login.style.display = 'none';
                register.style.display = 'block';
                loginAccount.style.display = 'block';
                createAccount.style.display = 'none';
            }
        });

        loginAccount.addEventListener( 'click', () => {
            if ( login && register ) {
                login.style.display = 'block';
                register.style.display = 'none';
                loginAccount.style.display = 'none';
                createAccount.style.display = 'block';
            }
        });
    }
});

function unlockAbilities( pointAmount ) {
    const unlockables = document.querySelectorAll( '[data-unlockable]' );


    if ( unlockables ) {
        unlockables.forEach( unlockable => {
          const whenToUnlock = unlockable.dataset.unlockable;

          if ( parseInt( pointAmount ) >= parseInt( whenToUnlock ) ) {
              // If spell, give spell ability.
              if ( 'explore-magic' === unlockable.dataset.genre ) {
                  navigator.vibrate(1000);

                  addNewSpell( unlockable.id );

                  // Remove if unlocked.
                  unlockable.remove();
              }
          }
        } );
    }
}

/**
 * Make npc follow walking path if it exists.
 *
 * @param npc
 */
function moveNPC( npc ) {
    const walkingPath = npc.dataset.path;
    const walkingSpeed = npc.dataset.speed;
    const repeatPath = npc.dataset.repeat;

    // Check if walking path exists.
    if ( walkingPath ) {
        const pathArray = JSON.parse( walkingPath );
        pathArray.unshift({'top': npc.style.top.replace('px', ''), 'left': npc.style.left.replace('px', '')});
        const pathCount = pathArray.length - 1;
        let position = 0;
        let nextPosition = 1;
        let loopCount = 0;
        let firstRun = true;

        if ( pathArray && 1 !== pathArray.length ) {
            window.walkingInterval = setInterval( () => {

                // Set next position to 0 if position is at the end.
                nextPosition = position === pathCount ? 0 : position + 1;

                console.log(position);

                // Get loop amount for how many times to loop interval before switching to next position.
                const loopAmount = getLoopAmount( pathArray[position].left, pathArray[position].top, pathArray[nextPosition].left, pathArray[nextPosition].top, walkingSpeed );

                // If loopAmount equals loop count, transition to next walking path.
                if ( loopCount === ( loopAmount - 1 ) || firstRun ) {
                    // Check that current position is not the last position. And move npc if it is not.
                    if ( pathCount > position || ( firstRun && pathCount === position ) ) {
                        regulateTransitionSpeed( pathArray[position].left, pathArray[position].top, pathArray[nextPosition].left, pathArray[nextPosition].top, npc, walkingSpeed );
console.log(pathArray[nextPosition]);
                        npc.style.left = pathArray[nextPosition].left + 'px';
                        npc.style.top = pathArray[nextPosition].top + 'px';
                    }

                    // If it is not the first run do this.
                    if ( false === firstRun ) {
                        // If the current position is not the last position, iterate on position count and reset loop count to 0.
                        if (pathCount > nextPosition) {
                            loopCount = 0;
                            firstRun = true;

                            if ( 0 !== nextPosition ) {
                                position++
                            } else {
                                position = 0;
                            }

                            // If it is the last position, and repeat is set to true, then reset position to 0.
                        } else if ('true' === repeatPath) {

                            console.log('repeat');
                            firstRun = true;
                            position = pathCount;
                            loopCount = 0;

                            // If not repeat and position is at end, clear interval.
                        } else {
                            clearInterval(window.walkingInterval);
                        }

                        // if it is the first run, set to false and iterate on position and loopcount.
                    } else {
                        firstRun = false;
                        loopCount++;
                    }
                } else {
                    loopCount++
                }
            }, 500);
        } else {
            regulateTransitionSpeed( npc.style.left.replace( 'px', '' ), npc.style.top.replace( 'px', '' ), pathArray[position].left, pathArray[position].top, npc, walkingSpeed );

            npc.style.left = pathArray[nextPosition].left + 'px';
            npc.style.top = pathArray[nextPosition].top + 'px';
        }
    }
}

/**
 * Adds points to user's account.
 *
 * @param amount
 * @param type
 * @param position
 * @param collectable
 */
function addUserPoints(amount, type, position, collectable) {
    // If collectable, remove from menu.
    if ( true === collectable ) {
        removeItemFromStorage(position, type);
    }

    // Make sure amount is always 100 or less. NOt for points.
    if ( amount > 100 && 'point' !== type ) {
        amount = 100;
    }

    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/add-explore-points/${currentUserId}/${position}/${amount}/${type}`;
    const bar = document.querySelector(`.${type}-amount`);
    let gauge = false;

    if ( bar ) {
        gauge = bar.querySelector( '.gauge' );
    }

    // Add to explorePoints.
    if ( explorePoints && explorePoints[type] && false === explorePoints[type].positions.includes(position) ) {
        explorePoints[type].positions.push( position );
    }

    if ( gauge && 'point' !== type ) {
        bar.setAttribute( 'data-amount', amount );
        gauge.style.width = amount + 'px';
    } else if ( 'point' === type ) {
        bar.setAttribute( 'data-amount', amount );

        gauge.style.width = getPointsGaugeAmount( amount );

        // Unlock abilities as points grow.
        unlockAbilities(amount);
    }

    if (restApiKey && '' !== position) {
        clearTimeout(pointTimeout);

        pointTimeout = setTimeout(() => {
            const xhr = new XMLHttpRequest();
            xhr.open( "GET", filehref, true );
            xhr.setRequestHeader( 'Content-Type', 'application/json' );
            xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
            xhr.send();
        }, 2000);
    }
}

/**
 * Get the gauge width for the points bar.
 * @param amount
 * @returns {string}
 */
function getPointsGaugeAmount( amount ) {
    return ( ( amount / window.nextLevelPointAmount ) * 100 ) + '%'
}

/**
 * Save mission once completed.
 * @param mission
 * @param value
 * @param position
 */
function saveMission( mission, value, position ) {
    // Cross off mission.
    const theMission = document.querySelector( '.' + mission + '-mission-item' );

    if ( theMission ) {
        const missionPoints = theMission.dataset.points;
        const nextMission = '' !== theMission.dataset.nextmission ? document.querySelector( '.' + theMission.dataset.nextmission + '-mission-item' ) : '';
        const missionBlockade = theMission.dataset.blockade;

        // Remove blockade if exists.
        if ( '' !== missionBlockade ) {
            document.querySelector( '.' + theMission.className.replace( 'mission-item ', '' ) + '-blockade' ).remove();
        }

        theMission.style.textDecoration = 'line-through';

        // If next mission existis then show it after previous is completed.
        if ( nextMission ) {
            nextMission.classList.add( 'engage' );
        }

        // Give points
        runPointAnimation(value, position, true, missionPoints);
    }

    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/mission/${mission}/${currentUserId}`;

    const xhr = new XMLHttpRequest();
    xhr.open( "GET", filehref, true );
    xhr.setRequestHeader( 'Content-Type', 'application/json' );
    xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
    xhr.send();
}

/**
 * Equip a new item to user.
 *
 * @param type
 * @param id
 * @param amount
 * @param unequip
 */
function equipNewItem(type, id, amount, unequip) {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/equip-explore-item/${type}/${id}/${amount}/${currentUserId}/${unequip}`;

    if (restApiKey) {
        clearTimeout(pointTimeout);

        pointTimeout = setTimeout(() => {
            const xhr = new XMLHttpRequest();
            xhr.open( "GET", filehref, true );
            xhr.setRequestHeader( 'Content-Type', 'application/json' );
            xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
            xhr.send();
        }, 2000);
    }
}

/**
 * Add new spell.
 *
 * @param id The spell id.
 */
function addNewSpell(id) {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/addspell/${currentUserId}/${id}`;

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();
    }
}

/**
 * Remove an item from the storage menu.
 *
 * @param position
 * @param type
 */
function removeItemFromStorage(position, type) {
    const menuItem = document.querySelector( '.retrieval-points span[title="' + position + '"]');
    const itemCount = menuItem.getAttribute('data-count');

    if ( menuItem ) {
        // if item count is above 1 then reduce count by 1 instead of removing.
        if ( itemCount && 1 < itemCount ) {
            menuItem.setAttribute( 'data-count', itemCount - 1 );
        } else {
            menuItem.setAttribute( 'data-type', '' );
            menuItem.setAttribute( 'data-id', '' );
            menuItem.setAttribute( 'data-value', '' );
            menuItem.setAttribute( 'title', '' );
            menuItem.setAttribute( 'data-empty', 'true' );
            menuItem.setAttribute( 'data-count', '' );
        }

        saveStorageItem( 0, position, type, 0, true );
    }
}

/**
 * Save settings
 *
 * @param music
 * @param sfx
 */
function saveSettings(music, sfx) {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/save-settings/${currentUserId}/${music}/${sfx}`;

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();
    }
}

/**
 * Save settings
 *
 * @param id
 * @param name
 * @param type
 * @param value
 * @param remove
 */
function saveStorageItem(id, name, type, value, remove) {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/save-storage-item/${currentUserId}/${id}/${name}/${type}/${value}/${remove}`;

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();
    }
}

/**
 * Adds points to user's account.
 *
 */
async function resetExplore() {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/resetexplore/${currentUserId}`;

    if (restApiKey) {
        const xhr = new XMLHttpRequest();
        xhr.open( "GET", filehref, true );
        xhr.setRequestHeader( 'Content-Type', 'application/json' );
        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
        xhr.send();
    }
}


/**
 * Save coordinates to user's account.
 *
 * @param left
 * @param top
 */
function addUserCoordianate(left, top) {
    const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/coordinates/${left.replace('px', '')}/${top.replace('px', '')}/${currentUserId}`;
    const xhr = new XMLHttpRequest();
    xhr.open( "GET", filehref, true );
    xhr.setRequestHeader( 'Content-Type', 'application/json' );
    xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
    xhr.send();
}

/**
 * Take health away from enemy.
 */
const hurtTheEnemy = (function () {
    let called = false;

    return function(theWeapon, value) {
        if (
            value && theWeapon && elementsOverlap( theWeapon.getBoundingClientRect(), value.getBoundingClientRect() )
        ) {
            if ( called === false ) {
                if ('explore-enemy' === value.getAttribute('data-genre') && false === theWeapon.classList.contains( 'protection' )) {
                    const enemyHealth = value.getAttribute('data-health');

                    // Kill enemy or lower health.
                    if ( 0 >= enemyHealth) {
                        clearInterval(window.shooterInt);
                        clearInterval(window.runnerInt);
                        value.remove();

                        // Save new health.
                        const position = cleanClassName(value.className);
                        const filehref = `https://${wpThemeURL}/wp-json/orbemorder/v1/enemy/${ position }/0/${ currentUserId }`;
                        const xhr = new XMLHttpRequest();
                        xhr.open( "GET", filehref, true );
                        xhr.setRequestHeader( 'Content-Type', 'application/json' );
                        xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
                        xhr.send();

                        theWeapon.classList.remove('engage');
                    } else {
                        const newHealth = 0 <= enemyHealth - 5 ? enemyHealth - 5 : 0;
                        value.setAttribute('data-health', newHealth );
                    }
                }

                called = true;

                // Reset called var.
                setTimeout(() => {
                    called = false;
                }, 1000);
            }
        }
    }
})();

/**
 * Pull new area html.
 *
 */
const enterNewArea = (function () {
    let called = false;

    return function(position, weapon, mapUrl) {
        // Clear enemy interval.
        clearInterval(window.shooterInt);
        clearInterval(window.runnerInt);
        clearInterval(window.walkingInterval);

        // Remove old items.
        const defaultMap = document.querySelector( '.default-map' );
        defaultMap.remove();

        // Don't repeat enter.
        if ( false === called ) {
            const filehref = `https://${ wpThemeURL }/wp-json/orbemorder/v1/area/${ position }/${ currentUserId }`;
            let newMusic = '';

            if ( musicNames ) {
                newMusic = musicNames[position];
            }

            if ( restApiKey ) {
                const xhr = new XMLHttpRequest();
                xhr.open( "GET", filehref, true );
                xhr.setRequestHeader( 'Content-Type', 'application/json' );
                xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
                xhr.send();

                xhr.addEventListener( "load", function ( e ) {
                    let newMapItems = JSON.parse( e.currentTarget.response );
                    newMapItems = JSON.parse( newMapItems.data );
                    const mapItemStyles = document.getElementById( 'map-item-styles' );
                    const chracterItem = document.getElementById( 'map-character' );
                    const container = document.querySelector( '.container' );
                    const head = document.querySelector( 'head' );

                    // Delete old area styles/maps.
                    if ( mapItemStyles ) {
                        mapItemStyles.remove();
                    }

                    const newStyles = document.createElement( 'style' );
                    newStyles.id = 'map-item-styles';
                    newStyles.innerHTML = newMapItems['map-item-styles-scripts'];


                    // Add area missions.
                    const missionList = document.querySelector( '.missions-content' );

                    if ( missionList ) {
                        missionList.innerHTML = newMapItems['map-missions'];
                    }

                    // Add new map styles and map urls.
                    if (head) {
                        head.append( newStyles );
                    }

                    // Replace items.
                    if ( defaultMap ) {
                        setTimeout(() => {
                            // Create new default map.
                            const newDefaultMap = document.createElement( 'div' );
                            newDefaultMap.className = 'default-map';
                            newDefaultMap.innerHTML = newMapItems['map-items'] + newMapItems['map-cutscenes'] + newMapItems['map-svg'];
    
                            if ( container ) {
                                container.append( newDefaultMap );

                                // Run no point class adder again
                                addNoPoints();
                            }

                            // Move npcs
                            const moveableCharacters = document.querySelectorAll( '.path-onload[data-path]:not([data-path=""])');

                            if ( moveableCharacters ) {
                                moveableCharacters.forEach( moveableCharacter => {
                                    moveNPC( moveableCharacter );
                                } );
                            }

                            // Load blockades.
                            loadMissionBlockades()
                        }, 700 );
                    }

                    setTimeout(() => {
                        chracterItem.style.top = newMapItems['start-top'] + 'px';
                        chracterItem.style.left = newMapItems['start-left'] + 'px';
                        weapon.style.display = "block";

                        const mapContainer = document.querySelector( '.container' );

                        mapContainer.className = 'container ' + position;
                        mapContainer.style.backgroundImage = 'url(' + mapUrl + ')';
                        playSong(newMusic);
                    }, 100 );
                } );
            }

            called = true;

            // Reset called var.
            setTimeout(() => {
                called = false;
            }, 1000);
        }
    }
})();

/**
 * Show loading screen.
 */
function startLoading() {
    const loadingScreen = document.querySelector( '.loading-screen' );

    if ( loadingScreen ) {
        loadingScreen.classList.add('engage');
    }
}

/**
 * Show loading screen.
 */
function stopLoading() {
    const loadingScreen = document.querySelector( '.loading-screen' );

    if ( loadingScreen ) {
        loadingScreen.classList.remove('engage');
    }
}


/**
 * Pull item description content.
 *
 */
const showItemDescription = (function () {
    let called = false;

    return function(item) {
        const id = item.getAttribute('data-id');

        // Remove engage from current.
        const currentSelectedItem = document.querySelector( '.storage-item.engage' );

        if ( currentSelectedItem ) {
            currentSelectedItem.classList.remove( 'engage' );
        }

        // Add engage to selected item.
        item.classList.add( 'engage' );

        // Don't repeat item get.
        if ( false === called ) {
            const filehref = `https://${ wpThemeURL }/wp-json/orbemorder/v1/get-item-description/${ id }/${currentUserId}`;

            if ( restApiKey ) {
                const xhr = new XMLHttpRequest();
                xhr.open( "GET", filehref, true );
                xhr.setRequestHeader( 'Content-Type', 'application/json' );
                xhr.setRequestHeader( 'Authorization', 'Basic ' + restApiKey );
                xhr.send();

                xhr.addEventListener( "load", function ( e ) {
                    let newItemDescription = JSON.parse( e.currentTarget.response );
                    newItemDescription = JSON.parse( newItemDescription.data );
                    const description = document.querySelector( '.retrieval-points #item-description' );
                    const selectedItem = document.querySelector( '.storage-item.engage' );

                    // Replace current description content.
                    description.innerHTML = newItemDescription;

                    // Add use and drop features.
                    const useButton = description.querySelector( '.use-button' );
                    const dropButton = description.querySelector( '.drop-button' );
                    const equipButton = description.querySelector( '.equip-button' );
                    const unequipButton = description.querySelector( '.unequip-button' );
                    const itemId = selectedItem.getAttribute( 'data-id' );
                    const name = selectedItem.getAttribute( 'title' );
                    const amount = selectedItem.getAttribute( 'data-value' );
                    const selectedType = selectedItem.getAttribute( 'data-type' );

                    // Add points.
                    if ( useButton && selectedItem ) {
                        if (100 > getCurrentPoints(selectedType) ) {
                            useButton.addEventListener( 'click', () => {
                                runPointAnimation( selectedItem, name );
                                description.innerHTML = '';
                            } );
                        }
                    }

                    // Drop item.
                    if ( dropButton ) {
                        dropButton.addEventListener( 'click', () => {
                            removeItemFromStorage(name, selectedType);
                            description.innerHTML = '';
                        } );
                    }

                    // Equip button.
                    if ( equipButton ) {
                        equipButton.addEventListener( 'click', () => {
                            const selectedItem = document.querySelector( '.storage-item.engage' );

                            // Update item class.
                            if ( selectedItem ) {
                                selectedItem.classList.add( 'equipped' );
                                selectedItem.classList.add( 'being-equipped' );
                            }

                            // Reset point calculations.
                            updatePointBars(false);

                            description.innerHTML = '';
                            equipNewItem( selectedType, itemId, amount, false );
                        } );
                    }

                    // Unequip.
                    if ( unequipButton ) {
                        unequipButton.addEventListener( 'click', () => {
                            const selectedItem = document.querySelector( '.storage-item.engage' );

                            // Update item class.
                            if ( selectedItem ) {
                                selectedItem.classList.remove( 'equipped' );
                                selectedItem.classList.add('unequip');
                            }

                            // Reset point calculations.
                            updatePointBars(true);

                            description.innerHTML = '';
                            equipNewItem( selectedType, itemId, amount, true );
                        } );
                    }
                } );
            }

            called = true;

            // Reset called var.
            setTimeout(() => {
                called = false;
            }, 1000);
        }
    }
})();

function updatePointBars(unequip) {
    const gear = document.querySelector( '.storage-item.being-equipped[data-type="gear"]' );
    const allWeapons = document.querySelector( '.store-item.being-equipped[data-type="weapons"]' );
    const healthBar = document.querySelector( `#explore-points .health-amount` );
    const manaBar = document.querySelector( `#explore-points .mana-amount` );
    let manaAmount = parseInt( manaBar.dataset.amount );
    let healthAmount = parseInt( healthBar.dataset.amount );
    let manaWidth = parseInt( manaBar.style.width.replace('px', '') );
    let healthWidth = parseInt( healthBar.style.width.replace('px', '') );

    // Calculate the gear modifiers.
    if ( gear && false === unequip ) {
        const gearAmount = gear.getAttribute( 'data-value' );
        const gearSubtype = gear.getAttribute( 'data-subtype' );

        if ( 'health' === gearSubtype ) {
            healthAmount += parseInt( gearAmount );
            healthWidth += parseInt( gearAmount );
        }

        if ( 'mana' === gearSubtype ) {
            manaAmount += parseInt( gearAmount );
            manaWidth += parseInt( gearAmount );
        }
    } else {
        const gearUnequip = document.querySelector( '.storage-item.unequip[data-type="gear"]' );
        const gearAmount = gearUnequip.getAttribute( 'data-value' );
        const gearSubtype = gearUnequip.getAttribute( 'data-subtype' );

        if ( 'health' === gearSubtype ) {
            healthAmount -= parseInt( gearAmount );
            healthWidth -= parseInt( gearAmount );
        }

        if ( 'mana' === gearSubtype ) {
            manaAmount -= parseInt( gearAmount );
            manaWidth -= parseInt( gearAmount );
        }
    }

    // update the points bars to new width.
    healthBar.style.width = healthWidth + 'px';
    healthBar.setAttribute( 'data-amount', healthAmount );
    healthBar.querySelector( '.gauge' ).style.width = healthAmount + 'px';

    manaBar.style.width = manaWidth + 'px';
    manaBar.setAttribute( 'data-amount', manaAmount );
    manaBar.querySelector( '.gauge' ).style.width = manaAmount + 'px';

    // Remove extra classes:
    const beingEquipped = document.querySelector( '.being-equipped' );
    const beingUnequipped = document.querySelector( '.unequip' );

    if ( beingEquipped ) {
        beingEquipped.classList.remove( 'being-equipped' );
    }

    if ( beingUnequipped ) {
        beingUnequipped.classList.remove( 'unequip' );
    }
}

/**
 * get current points.
 * @param type the type of point bar.
 */
function getCurrentPoints(type) {
    const thePoints = document.querySelector( `#explore-points .${ type }-amount` );

    return parseInt( thePoints.getAttribute('data-amount') );
}

function playSong(name) {
    const audio = document.getElementById(name);
    const volume = document.getElementById('music-volume');

    if ( volume && audio ) {
        audio.volume = volume.value / 100;
    }

    // Pause current song.
    if ( window.currentMusic ) {
        window.currentMusic.pause();
    }

    if ( audio ) {
        audio.play();

        // Set for volume control
        window.currentMusic = audio;
    }
}

/**
 * Start enemies.
 */
function engageEnemy( enemy ){
    const projSpeed = enemy.getAttribute('data-speed' );
    const enemyType = enemy.getAttribute( 'data-enemy-type' );

    if ( 'shooter' === enemyType ) {
        window.shooterInt = setInterval( () => {
            const mapCharacter = document.getElementById( 'map-character-icon' );
            const mapCharacterLeft = mapCharacter.getBoundingClientRect().left + mapCharacter.width / 2;
            const mapCharacterTop = mapCharacter.getBoundingClientRect().top + mapCharacter.width / 2;
            const projectile = enemy.querySelector( '.projectile' );

            if ( projectile ) {
                shootProjectile(projectile, mapCharacterLeft, mapCharacterTop, enemy, projSpeed, false, '.projectile');
            }
        }, (projSpeed * 1000) );
    }

    // Runner Type.
    if ( 'runner' === enemyType ) {
        window.runnerInt = setInterval( () => {
            const enemyName = cleanClassName(enemy.className);
            const newEnemy = document.querySelector( '.' + enemyName + '-map-item' );
            const collisionWalls = document.querySelectorAll( '.default-map svg rect, .protection' );
            let leftValInt = parseInt( newEnemy.style.left, 10 );
            let topValInt = parseInt( newEnemy.style.top, 10 );
            const mapCharacter = document.getElementById( 'map-character' );
            const mapCharacterLeft = parseInt(mapCharacter.style.left.replace('px', '')) + 500;
            const mapCharacterTop = parseInt(mapCharacter.style.top.replace('px', '')) + 500;

            // Move enemy left.
            if ( leftValInt < mapCharacterLeft ) {
                leftValInt = leftValInt + 1;
            } else {
                leftValInt = leftValInt - 1;
            }

            if ( topValInt < mapCharacterTop ) {
                topValInt = topValInt + 1;
            } else {
                topValInt = topValInt - 1;
            }

            if ( collisionWalls && newEnemy ) {
                const newBlockedPosition = getBlockDirection(collisionWalls, newEnemy.getBoundingClientRect(), topValInt, leftValInt, true);

                newEnemy.style.left = newBlockedPosition.left + 'px';
                newEnemy.style.top = newBlockedPosition.top + 'px';
            }
        }, 20 );
    }
}

/**
 * Shoot the projectile.
 * @param projectile
 * @param mapCharacterLeft
 * @param mapCharacterTop
 * @param enemy
 * @param projSpeed
 * @param spell
 * @param projectileClass
 */
function shootProjectile(projectile, mapCharacterLeft, mapCharacterTop, enemy, projSpeed, spell, projectileClass) {
    const newProjectile = projectile.cloneNode( true );

    // Move projectile.
    if (true !== spell) {
        moveEnemy( projectile, mapCharacterLeft, mapCharacterTop, projSpeed, enemy );
    } else {
        projectile.classList.remove( 'map-weapon' );
        projectile.classList.add( 'magic-weapon' )

        moveSpell(projectile, mapCharacterLeft, mapCharacterTop);
        enemy = document.querySelector( '.container' );
    }

    // check projectile position and remove if its wall.
    const projMovement = setInterval( function () {
        const projectile = enemy.querySelector( projectileClass );
        let collisionWalls = document.querySelectorAll( '.default-map svg rect, .protection, #map-character img' );

        if (true === spell) {
            collisionWalls = document.querySelectorAll( '.default-map svg rect, .enemy-item' );
        }

        if ( collisionWalls && projectile ) {
            collisionWalls.forEach( collisionWall => {
                if ( elementsOverlap( projectile.getBoundingClientRect(), collisionWall.getBoundingClientRect() ) ) {
                    projectile.remove();
                }
            } );
        }
    }, 20 );

    setTimeout( () => {
        if ( true === spell ) {
            const mapCharPos = document.getElementById( 'map-character' ).className.replace( '-dir', '');
            newProjectile.setAttribute( 'data-direction', mapCharPos );
        }

        enemy.appendChild( newProjectile );
        projectile.remove();
        clearInterval( projMovement );
    }, (projSpeed * 1000) - 500 );
}

/**
 *  Move enemy or projectile to character position.
 *
 * @param projectile The projectile or runner.
 * @param mapCharacterLeft Character's left position.
 * @param mapCharacterTop Character's right position.
 * @param projSpeed The speed of movement.
 * @param newEnemy The enemy shooting.
 */
function moveEnemy(projectile, mapCharacterLeft, mapCharacterTop, projSpeed, newEnemy) {
    let leftDifference = 0;
    let topDifference = 0;
    const projectilePosition = projectile.getBoundingClientRect();

    if ( projectilePosition.left < mapCharacterLeft ) {
        leftDifference = ((( mapCharacterLeft + 500 ) - projectilePosition.left)) + 'px';
    } else {
        leftDifference = '-' + ((projectilePosition.left - ( mapCharacterLeft - 500 ))) + 'px';
    }

    if ( projectilePosition.top < mapCharacterTop ) {
        topDifference = (( mapCharacterTop + 500 ) - projectilePosition.top) + 'px';
    } else {
        topDifference = '-' + (projectilePosition.top - ( mapCharacterTop - 500 ) + 'px');
    }

    const mapCharacter = document.getElementById( 'map-character' );
    const bPosition = getPositionAtCenter(newEnemy);
    const aPosition = getPositionAtCenter(mapCharacter);

    // Set the tranistion speed dynamically.
    regulateTransitionSpeed(aPosition.x, aPosition.y, bPosition.x, bPosition.y, projectile, 10);

    projectile.style.transform = 'translate(' + leftDifference + ', ' + topDifference + ')';
}

/**
 * Move enemy or projectile to character position.
 * @param projectile
 * @param mapCharacterLeft
 * @param mapCharacterTop
 */
function moveSpell(projectile, mapCharacterLeft, mapCharacterTop) {
    const aPosition = getPositionAtCenter(projectile);

    // Set the tranistion speed dynamically.
    regulateTransitionSpeed(aPosition.x, aPosition.y, mapCharacterLeft, mapCharacterTop, projectile, 1);

    projectile.style.left = mapCharacterLeft + 'px';
    projectile.style.top = mapCharacterTop + 'px';
}

/**
 *
 * @param aPositionx
 * @param aPositiony
 * @param bPositionx
 * @param bPositiony
 * @param projectile
 * @param multiple
 * @returns {number}
 */
function regulateTransitionSpeed(aPositionx, aPositiony, bPositionx, bPositiony, projectile, multiple) {
    const diffDist = Math.hypot(aPositionx - bPositionx, aPositiony - bPositiony);
    const transitionDist = diffDist * multiple;

    projectile.style.transition = 'all ' + transitionDist + 'ms linear 0s';
}

/**
 *
 * @param aPositionx
 * @param aPositiony
 * @param bPositionx
 * @param bPositiony
 * @param multiple
 * @returns {number}
 */
function getLoopAmount(aPositionx, aPositiony, bPositionx, bPositiony, multiple) {
    const diffDist = Math.hypot(aPositionx - bPositionx, aPositiony - bPositiony);
    const transitionDist = diffDist * multiple;

    return Math.ceil(transitionDist / 500);
}

/**
 *
 * @param element
 * @returns {{x: *, y: *}}
 */
function getPositionAtCenter(element) {
    const {top, left, width, height} = element.getBoundingClientRect();
    return {
        x: left + width / 2,
        y: top + height / 2
    };
}


/**
 * Helper function to add no points class to areas that have points already.
 */
function addNoPoints() {
    const types = ['health', 'mana', 'point', 'gear', 'weapons']

    types.forEach( type => {
        const selectedCharacterPositions = undefined !== explorePoints[type] ? explorePoints[type]['positions'] : [];

        // Add no point class to positions already gotten.
        if ( selectedCharacterPositions ) {
            selectedCharacterPositions.forEach( value => {
                const mapItem = document.querySelector('.' + value + '-map-item');

                if (mapItem) {
                    // If collected already don't show item.
                    if ('true' === mapItem.getAttribute('data-collectable') || 'true' === mapItem.getAttribute('data-breakable' ) ) {
                        mapItem.remove();
                    }

                    mapItem.classList.add('no-point');
                }
            } );
        }

        // Clear all bubbles.
        const characterSlugs = document.querySelectorAll('.map-item-modal.graeme');
        const allBubble = document.querySelectorAll('.map-item-modal');

        if ( allBubble ) {
            allBubble.forEach( bubble => {
                bubble.classList.remove( 'engage' );
            } );
        }

        // Show all character bubbles.
        if ( characterSlugs ) {
            characterSlugs.forEach( characterSlug => {
                characterSlug.classList.add( 'engage' );
            } );
        }
    });
}

/**
 * Engages the explore page game functions.
 */
function engageExploreGame() {
    const touchButtons = document.querySelector( '.touch-buttons' );

    // Hide start screen.
    document.querySelector( '.explore-overlay' ).remove();

    document.body.style.position = 'unset';
    if ( touchButtons ) {
        touchButtons.classList.add( 'do-mobile' );
    }

    // Start music.
    playSong( 'foresight' );

    // Show leave map link and keys guide.
    document.getElementById( 'explore-points' ).style.opacity = '1';
    document.getElementById( 'missions' ).style.opacity = '1';

    // Flash key-guide.
    const keyGuide = document.getElementById( 'key-guide' );
    spinMiroLogo( keyGuide, 'engage' );

    // Bring touch buttons forward and flash arrows.
    spinMiroLogo( touchButtons, 'engage' );

    // Run arrow flash intermittently.
    window.buttonShow = setInterval( function () {
        spinMiroLogo( touchButtons, 'engage' );
        spinMiroLogo( keyGuide, 'engage' );
    }, 10000 );

    // Move npcs
    const moveableCharacters = document.querySelectorAll( '.path-onload[data-path]:not([data-path=""])');

    if ( moveableCharacters ) {
        moveableCharacters.forEach( moveableCharacter => {
            moveNPC( moveableCharacter );
        } );
    }

    // Load blockades.
    loadMissionBlockades();

    // Add character hit button.
    addCharacterHit();

    // Update explore position if on explore page.
    movementIntFunc();

    // Update explore position if on explore page.
    setInterval( function () {
        const mapChar = document.querySelector( '#map-character' );
        const userLeft = mapChar.style.left;
        const userTop = mapChar.style.top;

        if ( userLeft !== window.currentUserLeft || userTop !== window.currentUserTop ) {
            addUserCoordianate( userLeft, userTop );

            window.currentUserLeft = userLeft;
            window.currentUserTop = userTop;
        }
    }, 1000 );
}

/**
 * Load blockades from missions.
 */
function loadMissionBlockades() {
    // Add mission blockade.
    const missions = document.querySelectorAll( '.mission-list .mission-item' );

    if ( missions ) {
        missions.forEach( mission => {
            const blockade = mission.dataset.blockade;

            if ( '' !== blockade ) {
                const blockadeSpecs = JSON.parse( blockade );
                const missionBlockadeEl = document.createElement( 'div' );
                const blockadeClasses = mission.className.replace( 'mission-item', '' );
                const defaultMap = document.querySelector( '.default-map' );

                missionBlockadeEl.className = 'wp-block-group map-item is-layout-flow wp-block-group-is-layout-flow' + blockadeClasses + '-blockade';
                missionBlockadeEl.style.top = blockadeSpecs.top + 'px';
                missionBlockadeEl.style.left = blockadeSpecs.left + 'px';
                missionBlockadeEl.style.width = blockadeSpecs.width + 'px';
                missionBlockadeEl.style.height = blockadeSpecs.height + 'px';

                // Add blockade to map.
                if ( defaultMap ) {
                    defaultMap.append( missionBlockadeEl );
                }
            }
        } );
    }
}

/**
 * Helper function that returns position of element.
 *
 * @param v
 * @param a
 * @param b
 * @param d
 * @param x
 * @param $newest
 * @returns {number}
 */
function miroExplorePosition(v,a,b,d,x, $newest) {
    const pane = document.querySelector( '.container' );
    const mapChar = document.querySelector( '#map-character' );
    const box = mapChar.querySelector( 'img' );
    const modal = document.querySelectorAll( '.map-item, .projectile, .enemy-item' );
    const boxSrc = box.getAttribute('src');
    let weaponEl = document.querySelector( '.map-weapon' );
    const magicEl = document.querySelector( '.magic-weapon' );

    // Reset weapon element as magic element.
    if ( magicEl ) {
        weaponEl = magicEl;
    }

    // Overlap check for map item.
    forEach(modal, function(index, value) {
        let position = cleanClassName(value.className);

        // For breakables.
        if ( weaponEl && 'true' === value.getAttribute('data-breakable') ) {
            if ( elementsOverlap( weaponEl.getBoundingClientRect(), value.getBoundingClientRect() ) ) {
                if ( value.dataset.mission && '' !== value.dataset.mission ) {
                    saveMission( value.dataset.mission, value, position );
                }

                value.remove();
            }

            return;
        }

        if ( value.classList.contains( 'enemy-item' ) ) {
            // Hurt enemy save enemy health.
            hurtTheEnemy( weaponEl, value );
        }

        if ( value && box && elementsOverlap( box.getBoundingClientRect(), value.getBoundingClientRect() ) ) {
            navigator.vibrate(1000);

            // If trigger. Trigger the triggee.
            if ( 'true' === value.dataset.trigger ) {
                const triggee = document.querySelector( '.' + value.dataset.triggee );
                // Start enemy attacks.

                if ( triggee ) {
                    engageEnemy( triggee );
                }
            }

            // NPC Walking Path Trigger.
            if ( true === value.classList.contains( 'path-trigger' ) && false === value.classList.contains( 'already-hit' ) ) {
                console.log('path trigger');
                const triggee = document.querySelector( '.' + value.getAttribute( 'data-triggee' ) );

                // Move triggered NPC.
                moveNPC( triggee );

                value.classList.add( 'already-hit' );
            }

            // If projectile collides with play than take health of player.
            if ( true === value.classList.contains( 'projectile' ) ) {
                const enemyValue = value.getAttribute('data-value');

                // Immediately remove the projectile when hits.
                const currentHealth =  document.querySelector( '#explore-points .health-amount' );

                if ( currentHealth && 0 < currentHealth.getAttribute( 'data-amount' ) ) {
                    const currentHealthLevel = currentHealth.getAttribute( 'data-amount' );
                    const newAmount = 1 < currentHealthLevel ? currentHealthLevel - enemyValue : 0;

                    addUserPoints( newAmount, 'health', 'projectile' );
                }
            }

            // For collectables.
            if ( 'true' === value.getAttribute('data-collectable') ) {
                if ( value.dataset.mission && '' !== value.dataset.mission ) {
                    saveMission( value.dataset.mission, value, position );
                }

                // Add item to storage menu.
                storeExploreItem( value );
            }

            // Check if collided point is enterable.
            if ( 'explore-area' === value.getAttribute( 'data-genre' ) ) {
                enterExplorePoint( value );
            }

            const cutsceneTriggee = value.getAttribute( 'data-triggee' );
            let theCutScene = document.querySelector('.' + position + '-map-cutscene');

            // Trigger cutscene if character overlapped with has a cutscene assigned to them in the area.
            if ( false === value.classList.contains( 'engage' ) && theCutScene && false === theCutScene.classList.contains( 'been-viewed' ) ) {
                if ( value.dataset.mission && '' !== value.dataset.mission ) {
                    saveMission( value.dataset.mission, value, position );
                }

                engageCutscene( position );
            }

            // Change position to triggee if cutscene trigger hit.
            position = cutsceneTriggee && '' !== cutsceneTriggee ? cleanClassName( cutsceneTriggee ) : position;
            theCutScene = document.querySelector('.' + position + '-map-cutscene');

            // Trigger cutscene if overlapping cutscene trigger item.
            if ( false === value.classList.contains( 'engage' ) && theCutScene && false === theCutScene.classList.contains( 'been-viewed' ) && true === value.classList.contains( 'cutscene-trigger' ) ) {
                if ( value.dataset.mission && '' !== value.dataset.mission ) {
                    saveMission( value.dataset.mission, value, position );
                }

                console.log('cutscene2');
                engageCutscene( position );
            }

            // remove item on collision if collectable.
            if ( 'true' === value.getAttribute( 'data-collectable' ) ) {
                value.remove();
            }
        } else if ( true === value.classList.contains(  'engage' ) ) {
           value.classList.remove( 'engage' );
        }
    });

    // Engage/disengage walking class.
    if (d[37] || d[38] || d[39] || d[40] || d[87] || d[65] || d[68] || d[83] ) {
        const cleanSrc = cleanWalk(boxSrc);
        const direction = getKeyByValue(d, true);
        const goThisWay = true === d[$newest] ? $newest : parseInt(direction);

        switch (goThisWay) {
            case 38 :
                if (false === boxSrc.includes('-back.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-back.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'top-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'top' );
                    }
                }
                break;
            case 37 :
                if (false === boxSrc.includes('-left.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-left.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'left-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'left' );
                    }
                }
                break;
            case 39 :
                if (false === boxSrc.includes('-right.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-right.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'right-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'right' );
                    }
                }
                break;
            case 40 :
                if (false === boxSrc.includes('avatar.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'down-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'down' );
                    }
                }
                break;
            case 87 :
                if (false === boxSrc.includes('-back.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-back.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'top-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'top' );
                    }
                }
                break;
            case 65 :
                if (false === boxSrc.includes('-left.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-left.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'left-dir' )
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'left' );
                    }
                }
                break;
            case 68 :
                if (false === boxSrc.includes('-right.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '-right.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'right-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'right' );
                    }
                }
                break;
            case 83 :
                if (false === boxSrc.includes('avatar.gif')) {
                    box.setAttribute( 'src', cleanSrc.replace( '.png', '.gif' ) );
                    mapChar.className = '';
                    mapChar.classList.add( 'down-dir' );
                    if ( weaponEl ) {
                        weaponEl.setAttribute( 'data-direction', 'down' );
                    }
                }
                break;
        }

        playWalkSound();
    } else {
        if (boxSrc.includes('gif')) {
            box.setAttribute( 'src', boxSrc.replace( 'gif', 'png' ) );

            stopWalkSound();
        }
    }

    const w = pane.offsetWidth - box.offsetWidth;
    const n = parseInt(v, 10) - (d[a] ? x : 0) + (d[b] ? x : 0);

    return n < 0 ? 0 : n > w ? w : n;

    function getKeyByValue(array, value) {
        for (var key in array) {
            if (array.hasOwnProperty(key) && array[key] === value) {
                return key;
            }
        }
        // If value is not found, you can return null or any other indicator
        return null;
    }
}

function cleanWalk(imageSrc) {
    return imageSrc.replace('-down', '').replace('-back', '').replace('-left', '').replace( '-right', '').replace('.gif', '.png');
}

/**
 * Add item to storage menu.
 *
 * @param item
 */
function storeExploreItem( item ) {
    const type = item.getAttribute('data-type');
    const value = item.getAttribute( 'data-value' );
    const id = item.id;
    const name = cleanClassName( item.className );
    const menuItem = document.createElement( 'span' );
    const menuType = getMenuType( type );
    const menu = document.querySelector( '[data-menu="' + menuType + '"]' );
    const thePoints = document.querySelector( `#explore-points .${ type }-amount` );
    let currentPoints = 100;

    if ( thePoints ) {
        currentPoints = thePoints.getAttribute( 'data-amount' );
    }

    if ( 'gear' !== type && ( 'health' === type || 'mana' === type ) && 100 > currentPoints ) {
        return;
    }

    // Add menu attributes.
    menuItem.setAttribute( 'data-type', type );
    menuItem.setAttribute( 'data-id', id );
    menuItem.setAttribute( 'data-value', value );
    menuItem.setAttribute( 'title', name );
    menuItem.setAttribute( 'data-empty', 'false' );

    // Item image.
    const itemImage = document.createElement( 'img' );
    itemImage.setAttribute( 'src', item.getAttribute( 'data-image' ) );
    itemImage.setAttribute( 'width', '30px' );
    itemImage.setAttribute( 'height', '30px' );

    if ( 'gear' === type ) {
        menuItem.append(itemImage);
    }
    menuItem.className = 'storage-item';

    // Add to menu.
    if ( menu ) {
        const emptyStorageItem = menu.querySelector('.storage-item[data-empty="true"]');
        const nonEmptyStorageItems = menu.querySelectorAll('.storage-item[data-empty="false"]');
        let isNewItem = true;

        // If empty slot exists then add new item to menu.
        if ( emptyStorageItem ) {
            emptyStorageItem.remove();

            // Check if item already exists and iterate if does.
            if ( nonEmptyStorageItems ) {
                nonEmptyStorageItems.forEach(nonEmptyStorageItem => {
                    const menuItemName = nonEmptyStorageItem.getAttribute( 'title' );

                    // If name is same, add count to item.
                    if ( menuItemName === name ) {
                        let currentCount = nonEmptyStorageItem.getAttribute( 'data-count' );

                        currentCount = null !== currentCount ? parseInt( currentCount ) + 1 : 2;
                        nonEmptyStorageItem.setAttribute( 'data-count', currentCount );
                        isNewItem = false;
                    }
                } );
            }

            if ( true === isNewItem ) {
                menu.prepend( menuItem );
                menuItem.addEventListener( 'click', () => {
                    showItemDescription(menuItem);
                });
            }

            // Add item to database.
            saveStorageItem(id, name, type, value, false);
        } else {
            //TODO CREATE NOTICE.
        }
    }
}

/**
 * cut scene logic
 */
function engageCutscene( position ) {
    const cutscene = document.querySelector('.' + position + '-map-cutscene');

    if ( cutscene ) {
        const dialogues = cutscene.querySelectorAll( 'p' );

        if ( false === cutscene.classList.contains( 'been-viewed' ) ) {
            // stop movement.
            window.allowMovement = false;
            cutscene.classList.add( 'engage' );
        }

        if (cutscene.classList.contains( 'engage' ) && false === cutscene.classList.contains( 'been-viewed' )) {
            cutscene.classList.add( 'been-viewed' );

            // Set allow be default.
            window.allowCutscene = true;

            // Before Cutscene.
            beforeCutscene(cutscene);

            // Close cutscene if click out.
            cutscene.addEventListener( 'click', ( e ) => {
                if ( false === cutscene.contains( e.target ) ) {
                    dialogues.forEach( dialogue => {
                        dialogue.classList.remove( 'engage' )
                    } );

                    cutscene.classList.remove( 'engage' );

                    // reset dialogue.
                    dialogues[0].classList.add( 'engage' );

                    // After cutscene.
                    afterCutscene( cutscene );
                }
            } );

            /**
             * Handles key events during a cutscene, allowing progression through dialogue and ending the cutscene.
             * @param {KeyboardEvent} event - The keyboard event object.
             */
            function cutsceneKeys ( event ) {
                if ( true === window.allowCutscene ) {
                    // Check if the pressed key is the spacebar (keyCode 32)
                    if ( event.keyCode === 32 ) {
                        window.allowMovement = true;

                        cutscene.classList.remove( 'engage' );
                        cutscene.removeEventListener( 'click', cutsceneKeys );
                        document.removeEventListener( 'keydown', cutsceneKeys );

                        // reset dialogue.
                        dialogues.forEach( dialogue => {
                            dialogue.classList.remove( 'engage' )
                        } );

                        dialogues[0].classList.add( 'engage' );

                        // After cutscene.
                        afterCutscene( cutscene );
                    }

                    if ( event.keyCode === 39 && dialogues && cutscene.classList.contains( 'engage' ) ) {
                        const currentDialogue = cutscene.querySelector( 'p.engage' );
                        const nextP = currentDialogue.nextElementSibling;

                        dialogues.forEach( dialogue => {
                            dialogue.classList.remove( 'engage' )
                        } );

                        if ( nextP ) {
                            nextP.classList.add( 'engage' );
                        } else {
                            // At end of dialogue. Close cutscene and make walking available.
                            cutscene.classList.remove( 'engage' );
                            cutscene.removeEventListener( 'click', cutsceneKeys );
                            document.removeEventListener( 'keydown', cutsceneKeys );

                            // Reengage movement.
                            window.allowMovement = true;

                            // reset dialogue.
                            dialogues[0].classList.add( 'engage' );

                            // After cutscene.
                            afterCutscene( cutscene );
                        }
                    }
                }
            }

            // Add a keydown event listener to the document to detect spacebar press
            document.addEventListener( 'keydown', cutsceneKeys );
        }
    }
}

/**
 * Stuff that happens before a cutscene.
 * @param cutscene
 */
function beforeCutscene( cutscene ) {
    const characterPosition = JSON.parse( cutscene.getAttribute( 'data-character-position' ) );

    if ( characterPosition && 0 < characterPosition.length && undefined !== characterPosition[0] ) {
        window.allowCutscene = false;
        // Trigger character move before cutscene starts.
        moveCharacter( document.getElementById( 'map-character' ), characterPosition[0].top, characterPosition[0].left, true, cutscene );
    }
}

/**
 * Stuff that happens after a cutscene.
 * @param cutscene
 */
function afterCutscene( cutscene ) {
    // Trigger walking path if selected and has path.
    const pathTriggerPosition = document.querySelector( '[data-trigger-cutscene="' + cleanClassName( cutscene.className ).replace( ' ', '' ) + '"]' );

    if ( pathTriggerPosition ) {
        moveNPC( pathTriggerPosition );
    }

    // Go to new area after cutscene if next area exists.
    const nextArea = cutscene.dataset.nextarea;
    const areaMap = cutscene.dataset.mapurl;
    const weapon = document.querySelector( '.map-weapon' );

    // If nextArea exists then trigger new area change.
    if ( nextArea ) {
        enterNewArea( nextArea, weapon, areaMap );
    }

    // If cutscene mission exists, trigger mission when cutscene ends.
    const mission = cutscene.dataset.mission;

    if ( mission && '' !== mission ) {
        const theMission = document.querySelector( '.' + mission + '-mission-item' );

        if ( theMission ) {
            theMission.classList.add( 'engage' );
        }
    }

    // Complete mission if cutscene has one.
    const missionComplete = cutscene.dataset.missioncomplete;

    if ( missionComplete ) {
        const missionCompleteMission = document.querySelector( '.' + missionComplete + '-mission-item' );
        saveMission( missionComplete, missionCompleteMission, missionComplete );
    }
}

function playWalkSound() {
    const walkingSound = document.getElementById('walking');

    walkingSound.loop = true;
    walkingSound.volume = window.sfxVolume;
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

/**
 * Enter an explore position if it is enterable.
 */
function enterExplorePoint(value) {
    // Add enter buttons to map items.
    const position = cleanClassName(value.className);
    const mapUrl = value.getAttribute( 'data-map-url' );

    // Add data point to button.
    const weaponEl = document.querySelector( '.map-weapon' );

    if ( weaponEl ) {
        weaponEl.style.display = "none";
    }

    enterNewArea(position, weaponEl, mapUrl);
}

/**
 * Handles character movement on the map interface.
 * This function sets up event listeners for keyboard and touch inputs
 * to enable character movement. It also continuously updates the character's
 * position on the map based on user input.
 */
function movementIntFunc() {
    console.log('start interval');
    const d = {};
    const x = 3;
    let $newest = false;
    window.allowMovement = true;

    // Add listeners for explore keyboard movement.
    document.addEventListener( 'keydown', function ( e ) {
        e.preventDefault();
        d[e.which] = true;

        $newest = e.which;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.addEventListener( 'keyup', function ( e ) {
        e.preventDefault();
        d[e.which] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );

    document.querySelector( '.top-left' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[37] = true;
        d[38] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.top-left' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[37] = false;
        d[38] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.top-middle' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[38] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.top-middle' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[38] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.top-right' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[38] = true;
        d[39] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.top-right' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[38] = false;
        d[39] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.middle-left' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[37] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.middle-left' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[37] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.middle-right' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[39] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.middle-right' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[39] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-left' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[37] = true;
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-left' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[37] = false;
        d[40] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-middle' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-middle' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[40] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-right' ).addEventListener( 'touchstart', function ( e ) {
        e.preventDefault();
        d[39] = true;
        d[40] = true;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );
    document.querySelector( '.bottom-right' ).addEventListener( 'touchend', function ( e ) {
        e.preventDefault();
        d[39] = false;
        d[40] = false;

        // Turn off arrow flash if character moved.
        clearInterval( window.buttonShow );
    } );

    window.movementInt = setInterval( function () {
        const box = document.getElementById( 'map-character' );
        const weapon = document.querySelector( '.map-weapon' );
        let leftVal = box.style.left;
        let topVal = box.style.top;
        const leftValInt = parseInt( leftVal, 10 );
        const topValInt = parseInt( topVal, 10 );
        const finalPos = blockMovement( topValInt, leftValInt );

        if ( window.allowMovement ) {
            box.style.top = miroExplorePosition( finalPos.top, d[87] ? 87 : 38, d[83] ? 83 : 40, d, x, $newest ).toString() + 'px';
            box.style.left = miroExplorePosition( finalPos.left, d[65] ? 65 : 37, d[68] ? 68 : 39, d, x, $newest ).toString() + 'px';

            if ( weapon ) {
                weapon.style.top = ( miroExplorePosition( finalPos.top, d[87] ? 87 : 38, d[83] ? 83 : 40, d, x, $newest ) + 500 ) + 'px';
                weapon.style.left = ( miroExplorePosition( finalPos.left, d[65] ? 65 : 37, d[68] ? 68 : 39, d, x, $newest ) + 500 ) + 'px';
            }
        }

        box.scrollIntoView();
    }, 20 );
}

/**
 * clean class name
 */
function cleanClassName(classes) {
    return classes.replace('wp-block-group map-item ', '')
        .replace('-map-item', '')
        .replace('wp-block-group enemy-item ', '')
        .replace(' no-point', '')
        .replace(' is-layout-flow', '')
        .replace(' wp-block-group-is-layout-flow', '')
        .replace( ' engage', '')
        .replace( 'wp-block-group map-cutscene ', '' )
        .replace( '-map-cutscene', '' )
        .replace( 'been-viewed', '' )
        .replace( ' path-onload', '' );
}

/**
 * Add character hit/interaction ability to spacebar (key 32).
 */
function addCharacterHit() {
    let weaponTime = 200;

    document.addEventListener('keydown', (event) => {
        const manaPoints = document.querySelector( `#explore-points .mana-amount` );
        const currentPoints = manaPoints.getAttribute( 'data-amount' );

        if ( 32 === event.keyCode ) {
            const weapon = document.querySelector( '.map-weapon' );

            if ( weapon ) {
                const isSpell = weapon.classList.contains( 'spell' );
                weaponTime = weapon.classList.contains( 'protection' ) ? 8000 : 200;

                // Only engage if not a spell or mana is not 0.
                if ( ( true === isSpell && 0 < currentPoints ) || false === isSpell ) {
                    weapon.classList.add( 'engage' );
                }

                // If spell, take manna away if above 0.
                if ( 0 < currentPoints && true === weapon.classList.contains( 'spell' ) ) {
                    // Use mana.
                    const objectAmount = weapon.getAttribute( 'data-value' );

                    // Remove amount to current points.
                    manaPoints.setAttribute( 'data-amount', parseInt( currentPoints ) - parseInt( objectAmount ) );

                    // Add class for notification of point gain.
                    manaPoints.classList.add( 'engage' );

                    // Get new amount.
                    let newAmount = parseInt( currentPoints ) - parseInt( objectAmount );
                    newAmount = 0 > newAmount ? 0 : newAmount;

                    // Add new point count to DB.
                    addUserPoints( newAmount, 'mana', 'magic' );

                    // Remove highlight on point bar.
                    setTimeout( () => {
                        manaPoints.classList.remove( 'engage' );
                    }, 500 );
                }

                setTimeout( () => {
                    weapon.classList.remove( 'engage' );
                }, weaponTime);

                // FOr shooting.
                if (0 < currentPoints && weapon && true === weapon.classList.contains('fire')) {
                    let weaponLeft = parseInt( weapon.style.left.replace( 'px', '' ) );
                    let weaponTop = parseInt( weapon.style.top.replace( 'px', '' ) );

                    const playerDirection = weapon.getAttribute( 'data-direction' );

                    switch ( playerDirection ) {
                        case 'down' :
                            weaponTop = weaponTop + 10000;
                            break;
                        case 'top' :
                            weaponTop = weaponTop - 10000;
                            break;
                        case 'left' :
                            weaponLeft = weaponLeft - 10000;
                            break;
                        case 'right' :
                            weaponLeft = weaponLeft + 10000;
                            break;
                    }

                    shootProjectile( weapon, weaponLeft, weaponTop, document, 2, true, '.magic-weapon' );
                }
            }
        }
    });
}

/**
 * Block movement if intersecting with the walls.
 * @param top
 * @param left
 * @returns {{top, left}}
 */
function blockMovement(top, left) {
    let finalTop = top;
    let finalLeft = left;
    const box = document.querySelector( '#map-character img' ).getBoundingClientRect();
    const collisionWalls = document.querySelectorAll('.default-map svg rect, .map-item:not([data-trigger="true"]), .enemy-item');

    return getBlockDirection(collisionWalls, box, finalTop, finalLeft, false);
}

/**
 * Get left and top locations to move collider.
 *
 * @param collisionWalls
 * @param box
 * @param finalTop The top position to move if not blocked.
 * @param finalLeft The left position to move if not blocked.
 * @param enemy The enemy.
 * @returns {{top: *, left: *, collide: *}}
 */
function getBlockDirection(collisionWalls, box, finalTop, finalLeft, enemy) {
    const left = finalLeft;
    const top = finalTop;
    let final = {top: finalTop, left: finalLeft, collide: false};

    if ( collisionWalls ) {
        collisionWalls.forEach( collisionWall => {
            collisionWall = collisionWall.getBoundingClientRect();

            if ( elementsOverlap( box, collisionWall ) ) {
                // set collide true since we're overlapping.
                final.collide = true;

                const topCollision = collisionWall.bottom > box.top && collisionWall.top < box.top && collisionWall.bottom < ( box.top + 10 );
                const bottomCollision = collisionWall.top < box.bottom && collisionWall.bottom > box.bottom && collisionWall.top > ( box.bottom - 10 );
                const leftCollision = collisionWall.right > box.left && collisionWall.left < box.left;
                const rightCollision = collisionWall.left < box.right && collisionWall.right > box.right;
                const adjust = true === enemy ? 5 : 5;

                if (topCollision && !bottomCollision) {
                    final =  { top: top + adjust, left: finalLeft, collide: true };
                } else if (bottomCollision && !topCollision) {
                    final = { top: top - adjust, left: finalLeft, collide: true};
                } else if (leftCollision && !rightCollision) {
                    final =  {top: finalTop, left: left + adjust, collide: true};
                } else if (rightCollision && !leftCollision) {
                    final = {top: finalTop, left: left - adjust, collide: true};
                }
            }
        } );
    }

    return final;
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

/**
 * Map for menu types.
 *
 * @param type type.
 */
function getMenuType( type ) {
    const menuTypes = {
        'health' : 'items',
        'mana' : 'items',
        'gear' : 'gear',
        'weapons' : 'weapons'
    }

    return menuTypes[type];
}

/**
 * Do the point animation stuff.
 *
 * @param value
 * @param position
 * @param isMission
 * @param missionPoints
 */
function runPointAnimation( value, position, isMission, missionPoints ) {
    value.classList.add( 'engage' );

    const positionType = true === isMission ? 'point' : value.getAttribute('data-type');

    if ( positionType ) {
        const thePoints = document.querySelector( `#explore-points .${ positionType }-amount` );
        let currentPoints = 100;
        const objectAmount = true === isMission ? parseInt(missionPoints) : value.getAttribute('data-value');

        if ( thePoints ) {
            currentPoints = thePoints.dataset.amount;
            if ( 'point' === positionType ) {
                const newPoints = parseInt( currentPoints ) + parseInt( objectAmount );

                // Add amount to current points.
                thePoints.setAttribute( 'data-amount', newPoints );

                // Add level check.
                const oldLevel = getCurrentLevel( currentPoints );
                const newLevel = getCurrentLevel( newPoints );
                window.nextLevelPointAmount = JSON.parse(levelMaps)[newLevel];

                // If new level is different than the old, then set UI to new.
                if ( oldLevel !== newLevel ) {
                    const currentLevelEl = document.querySelector( '.current-level' );

                    if ( currentLevelEl ) {
                        currentLevelEl.textContent = 'lvl. ' + newLevel;

                        const nextLevelPoints = document.querySelector( '.next-level-points' );

                        nextLevelPoints.textContent = window.nextLevelPointAmount;
                    }
                }

                // Update point count.
                const myPoints = document.querySelector( '.my-points' );


                if ( myPoints ) {
                    myPoints.textContent = newPoints;
                }
            }

            // Add class for notification of point gain.
            thePoints.classList.add( 'engage' );

            setTimeout( function () {
                thePoints.classList.remove( 'engage' );
            }, 2000 );

            // Check if it's a storage item.
            const collectable = value.classList.contains( 'storage-item' );

            // Play sound effect for points.
            playPointSound();

            // Add new point count to DB.
            addUserPoints( parseInt( currentPoints ) + parseInt( objectAmount ), positionType, position, collectable );
        }
    }
}

function playInterestSound() {
    const interestSound = document.getElementById('interest');

    interestSound.volume = window.sfxVolume;
    interestSound.play();

    return false;
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

    const pointSound = document.getElementById('ching');

    if (pointSound) {
        pointSound.volume = window.sfxVolume;
        pointSound.play();
    }

    return false;
}

/**
 * This will hold all in-game transport functionality.
 */
function engageTransportFunction() {
    const container = document.querySelector('.container');

    document.addEventListener( 'keydown', e => {
        // If Shift is pressed start transport sequence.
        if ( 16 === e.keyCode ) {
            container.addEventListener( 'click', clickTransport );
        }
    } );

    document.addEventListener( 'keyup', e => {
        if ( 16 === e.keyCode ) {
            container.removeEventListener( 'click', clickTransport );
        }
    } );
}

/**
 * Transport character.
 * @param clickE
 */
function clickTransport(clickE) {
    const container = document.querySelector('.container');
    const rect = container.getBoundingClientRect();
    const x = ( clickE.clientX - rect.left ) - 500;
    const y = ( clickE.clientY - rect.top ) - 500;
    const mapCharacter = document.getElementById( 'map-character' );
    const bar = document.querySelector('.power-amount');
    const gauge = bar.querySelector('.gauge');
    const powerAmount = bar.getAttribute( 'data-amount' );

    // Stop recharge.
    clearInterval(window.rechargeInterval);

    if (0 < powerAmount) {
        if ( mapCharacter && 'rect' !== clickE.target.tagName && false === clickE.target.classList.contains( 'map-item' ) ) {
            moveCharacter(mapCharacter, y, x, false, false);
        }

        const newAmount = powerAmount < 0 ? 0 : powerAmount - 25;

        bar.setAttribute('data-amount', newAmount)
        gauge.style.width = newAmount + 'px';
    }

    if (26 > powerAmount) {
        startPowerRecharge(gauge, bar);
    }
}

/**
 * Move the character.
 * @param mapCharacter
 * @param newTop
 * @param newLeft
 * @param gradual
 * @param cutscene
 */
function moveCharacter(mapCharacter, newTop, newLeft, gradual, cutscene ) {
    const currentLeft = parseInt(mapCharacter.style.left.replace( 'px', '' ));
    const currentTop = parseInt(mapCharacter.style.top.replace( 'px', '' ));

    // Top bigger/smaller.
    const leftBigger = currentLeft > newLeft;
    const topBigger = currentTop > newTop;
    const leftDiff = leftBigger ? currentLeft - newLeft : newLeft - currentLeft;
    const topDiff = topBigger ? currentTop - newTop : newTop - currentTop;
    let moveCount = 0;
    const box = mapCharacter.querySelector( 'img' );
	const weapon = document.querySelector( '.map-weapon' );

    if ( gradual ) {
        clearInterval( window.movementInt );

		// Add class to note movement.
		mapCharacter.classList.add( 'auto-move' );

        const biggestDiff = Math.max(topDiff, leftDiff);

        // Top move.
        const moveInt = setInterval( () => {
            if ( moveCount <= biggestDiff ) {
                let topDown = '';
                let leftRight = '';

                if ( topBigger ) {
                    mapCharacter.style.top = moveCount <= topDiff ? ( currentTop - moveCount ) + 'px' : newTop + 'px';
					weapon.style.top = ( parseInt( mapCharacter.style.top.replace('px', '') ) + 500 ) + 'px';
                    topDown = 'top';
                } else {
                    mapCharacter.style.top = moveCount <= topDiff ? ( currentTop + moveCount ) + 'px' : newTop + 'px';
					weapon.style.top = ( parseInt( mapCharacter.style.top.replace('px', '') ) + 500 ) + 'px';
                    topDown = 'down';
                }

                if ( leftBigger ) {
                    mapCharacter.style.left = moveCount <= leftDiff ? ( currentLeft - moveCount ) + 'px' : newLeft + 'px';
					weapon.style.left = ( parseInt( mapCharacter.style.left.replace('px', '') ) + 500 ) + 'px';
                    leftRight = 'left';
                } else {
                    mapCharacter.style.left = moveCount <= leftDiff ? ( currentLeft + moveCount ) + 'px' : newLeft + 'px';
					weapon.style.left = ( parseInt( mapCharacter.style.left.replace('px', '') ) + 500 ) + 'px';
                    leftRight = 'right';
                }

                // Change character image based on direction;
                directCharacter( topDown, leftRight, box, mapCharacter );

                mapCharacter.scrollIntoView();
            } else {
                // Reenable cutscene click events.
                window.allowCutscene = true;

				if ( false === cutscene ) {
					clearInterval( moveInt );
					movementIntFunc();
				} else if ( false === cutscene.classList.contains( 'engage' ) ) {
					clearInterval( moveInt );
					movementIntFunc();
				}
            }

            moveCount++
        }, 10 );
    } else {
        mapCharacter.style.left = newLeft + 'px';
        mapCharacter.style.top = newTop + 'px';
    }
}

function directCharacter( topDown, leftRight, box, mapCharacter ) {
    const cleanSrc = cleanWalk( box.getAttribute( 'src' ) );
    let direction = '' === topDown ? leftRight : topDown;

    if ( direction !== window.currentCharacterAutoDirection ) {

        window.currentCharacterAutoDirection = direction;

        direction = 'top' === direction ? 'back' : direction;
        mapCharacter.classList.add( direction + '-dir' );

		direction = 'down' === topDown ? '' : direction;

		box.setAttribute( 'src', cleanSrc.replace( '.png', ('' !== direction ? '-' + direction : '') + '.gif' ) );
		mapCharacter.className = '';
	}
}

/**
 * Get the current level.
 * @param currentPoints
 * @returns {number|string}
 */
function getCurrentLevel( currentPoints ) {
    if ( levelMaps ) {
        const levels = JSON.parse( levelMaps );

        for (const key in levels) {

            if (currentPoints > levels[key] && currentPoints < levels[parseInt(key) + 1] || currentPoints === levels[key]) {
                return parseInt(key) + 1
            }
        }
    }

    return 1;
}

/**
 *  Recharge power.
 * @param gauge
 * @param bar
 */
function startPowerRecharge(gauge, bar) {
    window.rechargeInterval = setInterval( () => {
        const currentAmount = parseInt(bar.getAttribute( 'data-amount' ));

        if (100 <= currentAmount ) {
            clearInterval(window.rechargeInterval);
        } else {
            bar.setAttribute( 'data-amount', currentAmount + 1 );
            gauge.style.width = (currentAmount + 1) + 'px';
        }
    }, 1500);
}
