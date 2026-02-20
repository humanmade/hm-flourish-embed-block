/**
 * Registers the flourish-embed block.
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl, SelectControl, Button, ToggleControl } from '@wordpress/components';
import SandboxedServerSideRender from './components/SandboxedServerSideRender';

registerBlockType( 'hm/flourish-embed', {
	title: 'Flourish Embed',
	description: 'Embed Flourish data visualizations',
	category: 'embed',
	icon: 'chart-line',
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const { type, id, fallbackImageId, useFallbackImageForRSS } = attributes;

		// Fetch media object from the store using the ID
		const media = useSelect(
			( select ) => fallbackImageId ? select( 'core' ).getMedia( fallbackImageId ) : null,
			[ fallbackImageId ]
		);

		const fallbackImageUrl = media?.source_url;

		return (
			<>
				<InspectorControls>
					<PanelBody title="Flourish Settings">
						<SelectControl
							label="Type"
							value={ type }
							options={ [
								{ label: 'Visualisation', value: 'visualisation' },
								{ label: 'Story', value: 'story' },
							] }
							onChange={ ( newType ) => setAttributes( { type: newType } ) }
						/>
						<TextControl
							label="ID"
							value={ id }
							onChange={ ( newId ) => setAttributes( { id: newId } ) }
							placeholder="Enter Flourish ID"
						/>
					</PanelBody>
					<PanelBody title="Fallback Image">
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( selectedMedia ) => setAttributes( {
									fallbackImageId: selectedMedia.id,
								} ) }
								allowedTypes={ [ 'image' ] }
								value={ fallbackImageId }
								render={ ( { open } ) => (
									<>
										{ fallbackImageUrl && (
											<div style={ { marginBottom: '10px' } }>
												<img src={ fallbackImageUrl } alt="Fallback" style={ { maxWidth: '100%', height: 'auto' } } />
											</div>
										) }
										<Button
											onClick={ open }
											variant="primary"
										>
											{ fallbackImageId ? 'Replace Image' : 'Upload Image' }
										</Button>
										{ fallbackImageId ? (
											<Button
												onClick={ () => setAttributes( {
													fallbackImageId: 0,
												} ) }
												variant="secondary"
												style={ { marginLeft: '10px' } }
											>
												Remove Image
											</Button>
										) : null }
									</>
								) }
							/>
						</MediaUploadCheck>
						{ fallbackImageId > 0 && (
							<div style={ { marginTop: '15px' } }>
								<ToggleControl
									label="Use in RSS Feeds"
									help={ useFallbackImageForRSS ? 'Fallback image will be shown in RSS feeds' : 'Standard Flourish embed will be shown in RSS feeds' }
										checked={ useFallbackImageForRSS }
										onChange={ ( newValue ) => setAttributes( { useFallbackImageForRSS: newValue } ) }
								/>
							</div>
						) }
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<SandboxedServerSideRender
						block="hm/flourish-embed"
						attributes={ attributes }
						messages={ {
							emptyResponse: 'Please enter a Flourish ID to preview the embed.',
						} }
					/>
				</div>
			</>
		);
	},
	save: () => null, // Dynamic block - rendered server-side
} );
