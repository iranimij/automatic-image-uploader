import React, {useEffect, useState} from 'react';
import { render } from 'react-dom';
import {
	Button,
	Card,
	CardContent,
	Switch,
	FormControlLabel, FormGroup,
} from '@mui/material';
import { __ } from '@wordpress/i18n';
import { nonce, uploaderIsEnabled } from '@aiu';
import './style.scss';

const App = () => {
	const [ enableUploader, setEnableUploader ] = useState( false );
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

	return (
		<React.Fragment>
			<Card>
				<div className="col-md-12 aiu-settings-header">
					<h1>{ __( 'Settings', 'aiu' ) }</h1>
				</div>
				<form onSubmit={ onSubmit } >
					<CardContent>
						<FormGroup>
							<FormControlLabel

								control={
									<Switch onChange={ handleChange } defaultChecked={ uploaderIsEnabled == 'true' ? true : false } />
								}
								label={ __( 'Enable uploader', 'aiu' ) }
							/>
						</FormGroup>
					</CardContent>
					<div className="aiu-form-button">
						<Button variant="contained" color="success" type="submit" className="mb-2">
							{ __( 'Save', 'aiu' ) }
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
