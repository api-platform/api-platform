import React, { Component } from 'react';
import { HydraAdmin } from '@api-platform/admin';

class App extends Component {
  render() {
    return <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}/> // Replace with your own API entrypoint
  }
}

export default App;
