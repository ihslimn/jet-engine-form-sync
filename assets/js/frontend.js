( function( $ ) {

	"use strict";

    document.addEventListener( 'DOMContentLoaded', function() {
		
		const {
			addAction,
		} = window.JetPlugins.hooks;

		window.JetFormSyncData = {};
		
		addAction( 'jet.fb.observe.after', 'form-sync/onSubmit', init );

		window.JetPlugins.hooks.addFilter(
			'jet-smart-filters.request.data',
			'jet-engine/listing-popup/additional-data',
			function( data ) {
				if ( JetFormSyncData.listingPopupData !== false ) {
					data.extra_props ??= {};
					data.extra_props.jetEngineListingPopupData = JetFormSyncData.listingPopupData;
				}

				return data;
			}
		);
		
		window.JetPlugins.hooks.addFilter(
			'jet-popup.show-popup.data',
			'jet-engine/listing-popup/additional-data',
			( popupData, $popup, $triggeredBy ) => {
				if ( ! popupData?.isJetEngine ) {
					return popupData;
				}

				JetFormSyncData.listingPopupData = {
					popupData: popupData,
					extra: {
						page_url: window.location.href,
						popup_id: popupData.popupId.replace( 'jet-popup-', '' ),
					}
				};

				return popupData;
			}, 100
		);
	
		function init( observable ) {
	
			//save form id to status ReactiveVar
			observable.form.submitter.status.formId = observable.form.getFormId();
			
			observable.form.submitter.status.watch( onFormSubmit );
			
		}
		
		function onFormSubmit() {
			//log status and form id to console
			//console.log( this.current, this.formId );

            const event = new CustomEvent(
				'jet-engine/form-sync/submit/' + this.formId,
				{
					detail: {
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

				switch ( this?.provider ) {
					case 'jet-popup':
						this.dataValue = Date.now();

						if ( ! JetFormSyncData?.listingPopupData ) {
							return;
						}

						let popupData = JetFormSyncData.listingPopupData.popupData;
						let $popup = jQuery( '#' + popupData.popupId );

						if ( ! $popup[0] ) {
							return;
						}

						popupData.page_url = JetFormSyncData.listingPopupData.extra.page_url;
						popupData.popup_id = JetFormSyncData.listingPopupData.extra.popup_id;

						let filterGroup = this.filterGroup;

						filterGroup.startAjaxLoading();

						$.ajax( {
							url: window.jetPopupData.ajax_url,
							type: 'POST',
							dataType: 'json',
							data: {
								action : 'form_sync_ajax_popup',
								data : popupData
							},
						} ).done( function( response ) {
							$( '#' + popupData.popupId + ' .jet-popup__container-content' ).html( response.content.content );
							JetPopupFrontend.maybeElementorFrontendInit( $( '#' + popupData.popupId + ' .jet-popup__container-content' ) );
							filterGroup.endAjaxLoading();
						} ).fail( function( response ) {
							console.log( response );
							filterGroup.endAjaxLoading();
						} );

						return;
					default:
						// do nothing
				}
				console.log( e );
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
