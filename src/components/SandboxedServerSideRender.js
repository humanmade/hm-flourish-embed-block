/**
 * SandboxedServerSideRender component
 *
 * Renders server-side block content in an iframe using SandBox.
 */

import { SandBox } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useState, useEffect } from '@wordpress/element';
import { addQueryArgs } from '@wordpress/url';
import { Spinner } from '@wordpress/components';

export default function SandboxedServerSideRender( {
	block,
	attributes = {},
	messages = {}
} ) {
	const defaultMessages = {
		emptyResponse: 'No content to display',
		error: 'Error',
		...messages,
	};

	const [ html, setHtml ] = useState( '' );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		setIsLoading( true );
		setError( null );

		const path = addQueryArgs( `/wp/v2/block-renderer/${ block }`, {
			context: 'edit',
			sandboxedPreview: '1',
		} );

		apiFetch( {
			path,
			method: 'POST',
			data: {
				attributes,
			},
		} )
			.then( ( response ) => {
				setHtml( response.rendered || '' );
				setIsLoading( false );
			} )
			.catch( ( err ) => {
				setError( err.message || 'Unknown error' );
				setIsLoading( false );
			} );
	}, [ block, JSON.stringify( attributes ) ] );

	if ( isLoading ) {
		return (
			<div style={ { textAlign: 'center', padding: '2em' } }>
				<Spinner />
			</div>
		);
	}

	if ( error ) {
		return (
			<div style={ { padding: '1em', border: '1px solid #dc3232', backgroundColor: '#fef7f7' } }>
				<strong>{ defaultMessages.error }:</strong> { error }
			</div>
		);
	}

	if ( ! html ) {
		return (
			<div style={ { padding: '1em', border: '1px solid #ddd', backgroundColor: '#f9f9f9' } }>
				{ defaultMessages.emptyResponse }
			</div>
		);
	}

	return <SandBox html={ html } />;
}
