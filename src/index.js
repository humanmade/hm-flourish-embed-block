/**
 * Registers the flourish-embed block.
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import SandboxedServerSideRender from './components/SandboxedServerSideRender';

registerBlockType( 'hm/flourish-embed', {
	title: 'Flourish Embed',
	description: 'Embed Flourish data visualizations',
	category: 'embed',
	icon: 'chart-line',
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const { type, id } = attributes;

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
