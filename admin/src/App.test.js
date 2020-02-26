import React from 'react';
import { render } from '@testing-library/react';
import App from './App';

test('renders loader', () => {
  const { getByText } = render(<App />);
  const divElement = getByText(/The page is loading, just a moment please/i);
  expect(divElement).toBeInTheDocument();
});
