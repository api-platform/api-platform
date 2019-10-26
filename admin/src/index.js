import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import * as serviceWorker from './serviceWorker';

ReactDOM.render(<App />, document.getElementById('root'));
serviceWorker.unregister();

/* If you want your app to work offline and load faster, you can change
 unregister() => register() in line no.7 . Note this comes with some pitfalls.
 Learn more about service workers: http://bit.ly/CRA-PWA */


