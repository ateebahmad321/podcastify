const Podcastify = window.Podcastify || {};
/**
 *  General ajax function for podcastify.
 * 
 * @since 1.0.0
 * 
 * @param {object} args 
 * @param {function} beforeCall 
 * @param {function} onSuccess 
 * @param {function} afterCompletion 
 */
const wppAjaxCall = ( args , beforeCall = () =>{}, onSuccess =  () =>{}, onError = () =>{} ) => {


	const formData = new FormData();
	
	for ( const [key, value] of Object.entries( args.requestData || {} ) ) {
		formData.append( `${key}` , `${value}` );
	}

	//Before call start.
	beforeCall();

	fetch( args.requestURL || Podcastify.ajaxUrl, {
		method: args.method || 'POST',
		body: formData
	} )
		.then( response => response.json() )
		.then( result => {
			onSuccess( result );
		} )
		.catch( error => {
			onError( error );
			console.error( 'Error:', error );  /* eslint-disable-line */
		} );
};


/**
 * Set episode meta.
 * 
 * @since 1.0.0
 * 
 * @param {string}  metaKey 
 * @param {string} metaValue 
 * @param {object} args 
 */
const wppSetMeta = ( metaKey , metaValue = '', args = {} ) => { /* eslint-disable-line */

	args.requestData.security = args.requestData.ajaxSecurity || Podcastify.ajaxSecurity;
	args.beforeCall = args.beforeCall || ( () => {} );
	args.onSuccess = args.onSuccess || ( () => {} );
	args.onError = args.onError || ( () => {} );

	wppAjaxCall( args, args.beforeCall, args.onSuccess, args.onError );
};

export { wppAjaxCall, wppSetMeta };