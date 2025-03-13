( function( $ ) {

	"use strict";

    document.addEventListener( 'DOMContentLoaded', function() {
		
		const {
			addAction,
		} = window.JetPlugins.hooks;
		
		addAction( 'jet.fb.observe.after', 'form-sync/onSubmit', init );
		addAction( 'jfb.formless.submit', 'form-sync/formless', onFormless );

		function onFormless( response ) {
			dispatchEvent( response?.data?.__submitted_form_id, response.code );
		}

		function init( observable ) {
			
			if ( ! observable?.form?.submitter?.status ) {
				return;
			}
			//save form id to status ReactiveVar
			observable.form.submitter.status.observable = observable;
			
			observable.form.submitter.status.watch( onFormSubmit );
			
		}

		function dispatchEvent( formId = false, status = 'success' ) {
			if ( ! formId ) {
				return;
			}

			const event = new CustomEvent(
				'jet-engine/form-sync/submit/' + formId,
				{
					detail: {
                        status: status
					},
				}
			);
			
			document.dispatchEvent( event );
		}
		
		function onFormSubmit() {			
            dispatchEvent( this.observable.form.getFormId(), this.current );
		}
	} );

	let queryVarIndex = 1;

	const initFormSyncFilter = function() {

		window.JetSmartFilters.filtersList.JetEngineFormSync = 'jet-smart-filters-form-sync';
		window.JetSmartFilters.filters.JetEngineFormSync = class JetEngineFormSync extends window.JetSmartFilters.filters.BasicFilter {

			name = 'form-sync';
            formId = '';
            filterOn = 'success';

			constructor( $container ) {
				
				const $filter = $container.find( '.jet-smart-filters-form-sync' );
				
				super( $container, $filter );

				//ensure multiple filters work for the same provider
				this.queryVar = queryVarIndex++;
				
				this.formId = $container.data( 'form-id' );

                if ( ! this.formId ) {
                    return;
                }

                if ( $container.data( 'filter-on' ) ) {
                    this.filterOn = $container.data( 'filter-on' );
                }

				document.addEventListener( 'jet-engine/form-sync/submit/' + this.formId, this.sync.bind( this ) );
			}

			processData() {
			}

            sync( e ) {
                if ( this.filterOn === 'success' && e?.detail?.status !== 'success' ) {
                    return;
                }

                this.dataValue = Date.now();
				this.wasChanged ? this.wasChanged() : this.wasСhanged();
            }

			reset() {
				// Left empty to prevent reset when clicking the Remove filters button
			}

		};

	}

	
    document.addEventListener( 'jet-smart-filters/before-init', ( e ) => {
        initFormSyncFilter();
    });
	

}( jQuery ) );
