import React from 'react';
import { HydraAdmin } from '@api-platform/admin';

export default () => <HydraAdmin entrypoint={process.env.REACT_APP_API_ENTRYPOINT}/>;
