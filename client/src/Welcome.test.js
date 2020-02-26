import React from 'react';
import { render } from '@testing-library/react';
import App from './Welcome';

test('renders API Platform title', () => {
  const { getByText } = render(<App />);
  const strongElement = getByText(/API Platform/i);
  expect(strongElement).toBeInTheDocument();
});
