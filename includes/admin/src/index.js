import React, { useState } from 'react';
import { render } from 'react-dom';
import {
	Button,
	Card,
	CardContent,
	Switch,
	FormControlLabel, FormGroup,
} from '@mui/material';
import { __ } from '@wordpress/i18n';
import { nonce } from '@aiu';
import './style.scss';
import {useForm, Controller} from "react-hook-form";


const App = () => {
	const [ enableUploader, setEnableUploader ] = useState( false );

	const onSubmit = ( e ) => {
		e.persist();
		e.preventDefault();

		const data = {
			enable_uploader: enableUploader,
			nonce,
			action: 'aiu_save_settings',
		};

		wp.ajax.post( data ).done( ( message ) => {
			console.log('done',message);
		} ).fail( ( error ) => {
			// eslint-disable-next-line no-console
			console.log( error );
		} );
	};

	const handleChange = ( e ) => {
	  e.persist();
	  setEnableUploader( e.target.checked );
	}

	return (
		<React.Fragment>
			<Card>
				<form onSubmit={ onSubmit } >
					<CardContent>
						<FormGroup>
							<FormControlLabel
								control={
									<Switch onChange={ handleChange }/>
								}
								label={ __( 'Enable uploader', 'aiu' ) }
							/>
						</FormGroup>
					</CardContent>
					<div className="aiu-form-button">
						<Button variant="contained" color="success" type="submit">
							{ __( 'Save', 'aiu' ) }
						</Button>
					</div>
				</form>
			</Card>
		</React.Fragment>
	);
};

render( <App />, document.getElementById( 'aiu-root' ) );
