"use strict";

document.addEventListener("DOMContentLoaded", function(){
	// Repeater field functionality.
	const repeaterContainers = document.querySelectorAll( '.repeater-container' );

	if ( repeaterContainers ) {
		repeaterContainers.forEach( repeaterContainer => {
			const containerWrap = repeaterContainer.querySelector( '.field-container-wrap' );
			const addField = repeaterContainer.querySelector( '.add-field' );
			const removeFields = repeaterContainer.querySelectorAll( '.remove-field' );

			// Remove field.
			if ( removeFields ) {
				removeFields.forEach( removingField => {
					removeField( removingField, repeaterContainer );
				} );
			}

			// Add new field.
			if ( addField ) {
				addField.addEventListener( 'click', () => {
					const fieldContainers = repeaterContainer.querySelectorAll( '.field-container' );
					const oldInput = fieldContainers[0].querySelector( 'input' );
					const newField = fieldContainers[0].cloneNode( true );
					const newFieldInputs = newField.querySelectorAll( 'input' );
					const oldName = oldInput.getAttribute( 'name' );
					const fieldIndex = fieldContainers.length;
					let newName = oldName.replace( '0', fieldIndex );
					newField.querySelector( '.container-index' ).textContent = fieldIndex;

					if ( newFieldInputs ) {
						newFieldInputs.forEach( ( newFieldInput, index ) => {
							newFieldInput.value = 0;

							if ( 0 !== index ) {
								newName = newName.replace( 'top', 'left' );
							}

							newFieldInput.setAttribute( 'data-index', fieldIndex);
							newFieldInput.setAttribute( 'name', newName );
							newFieldInput.id = newName;
						} );
					}

					containerWrap.appendChild( newField );
					const newestRemove = newField.querySelector( '.remove-field' );

					removeField( newestRemove, repeaterContainer );
				});
			}
		} );
	}

	function removeField( removeField, repeaterContainer ) {
		const fieldContainers = repeaterContainer.querySelectorAll( '.field-container' );

		removeField.addEventListener( 'click', () => {
			const closestContainer = removeField.closest( '.field-container' );

			// Remove.
			closestContainer.remove();

			if ( closestContainer ) {
				// Reset Indexes.
				const fieldContainersNew = repeaterContainer.querySelectorAll( '.field-container' );
				const firstInput = fieldContainers[0].querySelector( 'input' );

				if ( fieldContainers ) {

					fieldContainersNew.forEach( ( fieldContainer, index ) => {
						const fcInputs = fieldContainer.querySelectorAll( 'input' );
						const containerIndex = fieldContainer.querySelector( '.container-index' );
						const oldIndex = containerIndex.textContent;

						if ( containerIndex ) {
							containerIndex.textContent = index;
						}

						fcInputs.forEach( fcInput => {
							const firstInputName = fcInput.id.replace( oldIndex, index );
							fcInput.setAttribute( 'data-index', index );
							fcInput.id = firstInputName;
							fcInput.setAttribute( 'name', firstInputName );
						} );
					} );
				}
			}
		} );
	}
});