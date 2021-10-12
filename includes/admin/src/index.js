import React from 'react';
import { render } from 'react-dom';
import {Button, Card, CardActions, CardContent, Pagination, Typography} from '@mui/material';

const App = () => {
	return <Card sx={{ minWidth: 275 }}>
		<CardContent>
			test
		</CardContent>
		<CardActions>
			<Button color="secondary">Secondary</Button>
			<Button variant="contained" color="success">
				Success
			</Button>
			<Button variant="outlined" color="error">
				Error
			</Button>
		</CardActions>
	</Card>;
};

render( <App />, document.getElementById( 'aiu-root' ) );
