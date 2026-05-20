import { HydraAdmin } from '@api-platform/admin';

const entrypoint =
  (import.meta.env.VITE_ENTRYPOINT as string | undefined) ??
  window.location.origin;

export const App = (): JSX.Element => (
  <HydraAdmin entrypoint={entrypoint} title="API Platform admin" />
);
