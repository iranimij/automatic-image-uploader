import React, { useEffect, useState } from 'react';
import { render } from 'react-dom';
import {
	Button,
	Card,
	CardContent,
	Switch,
	FormControlLabel, FormGroup,
} from '@mui/material';
import { __ } from '@wordpress/i18n';
import { nonce, aiuOptions } from '@aiu';
import './style.scss';

const App = () => {
	const [ enableUploader, setEnableUploader ] = useState( aiuOptions?.aiu_enable_uploader );
	const [ firstImageIsThumbnail, setFirstImageIsThumbnail ] = useState( aiuOptions?.set_first_image_as_thumbnail );
	const [ saveMessage, setSaveMessage ] = useState( false );

	useEffect( () => {
		setTimeout( () => {
			setSaveMessage( false );
		}, 2000 );
	}, [ saveMessage ] );

	const onSubmit = ( e ) => {
		e.persist();
		e.preventDefault();

		const data = {
			enable_uploader: enableUploader,
			first_image_is_thumbnail: firstImageIsThumbnail,
			nonce,
			action: 'aiu_save_settings',
		};

		wp.ajax.post( data ).done( ( message ) => {
			setSaveMessage( message );
		} ).fail( ( error ) => {
			// eslint-disable-next-line no-console
			console.log( error );
		} );
	};

	const handleChange = ( e ) => {
		e.persist();
		setEnableUploader( e.target.checked );
	};

	const handleSetThumbnailChange = ( e ) => {
		e.persist();
		setFirstImageIsThumbnail( e.target.checked );
	};

	return (
		<React.Fragment>
			<Card>
				<div className="col-md-12 aiu-settings-header">
					<h1>{ __( 'Settings', 'automatic-image-uploader' ) }</h1>
				</div>
				<form onSubmit={ onSubmit } >
					<CardContent>
						<FormGroup>
							<FormControlLabel

								control={
									// eslint-disable-next-line camelcase
									<Switch onChange={ handleChange } defaultChecked={ aiuOptions?.aiu_enable_uploader == 'true' ? true : false } />
								}
								label={ __( 'Enable uploader', 'automatic-image-uploader' ) }
							/>
						</FormGroup>
						<FormGroup>
							<FormControlLabel

								control={
									// eslint-disable-next-line camelcase
									<Switch onChange={ handleSetThumbnailChange } defaultChecked={ aiuOptions?.set_first_image_as_thumbnail == 'true' ? true : false } />
								}
								label={ __( 'Set first image as thumbnail', 'automatic-image-uploader' ) }
							/>
						</FormGroup>
					</CardContent>
					<div className="aiu-form-button">
						<Button variant="contained" color="success" type="submit" className="mb-2">
							{ __( 'Save', 'automatic-image-uploader' ) }
						</Button>
						<div>
							{ ! _.isEmpty( saveMessage ) ? saveMessage : '' }
						</div>
					</div>
				</form>
			</Card>
		</React.Fragment>
	);
};

render( <App />, document.getElementById( 'aiu-root' ) );
