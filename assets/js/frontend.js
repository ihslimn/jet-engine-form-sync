( function( $ ) {

	"use strict";

    document.addEventListener( 'DOMContentLoaded', function() {
		
		const {
			addAction,
		} = window.JetPlugins.hooks;
		
		addAction( 'jet.fb.observe.after', 'form-sync/onSubmit', init );
	
		function init( observable ) {
	
			//save form id to status ReactiveVar
			observable.form.submitter.status.formId = observable.form.getFormId();
			
			observable.form.submitter.status.watch( onFormSubmit );
			
		}
		
		function onFormSubmit() {
			//log status and form id to console
			//console.log( this.current, this.formId );

            const event = new CustomEvent(
				"jet-engine/form-sync/submit",
				{
					detail: {
						formId: this.formId,
                        status: this.current
					},
				}
			);
			
			document.dispatchEvent( event );
		}
	} );

	const initFormSyncFilter = function() {

		window.JetSmartFilters.filtersList.JetEngineFormSync = 'jet-smart-filters-form-sync';
		window.JetSmartFilters.filters.JetEngineFormSync = class JetEngineFormSync extends window.JetSmartFilters.filters.BasicFilter {

			name = 'form-sync';
            formId = '';
            filterOn = 'success';

			constructor( $container ) {
				
				const $filter = $container.find( '.jet-smart-filters-form-sync' );
				
				super( $container, $filter );
				
				this.formId   = $container.data( 'form-id' );
                this.filterOn = $container.data( 'filter-on' );

				document.addEventListener( 'jet-engine/form-sync/submit', this.sync.bind( this ) );
			}

			processData() {
			}

            sync( e ) {
                if ( ! e?.detail?.formId || + e.detail.formId !== + this.formId ) {
                    return;
                }

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
