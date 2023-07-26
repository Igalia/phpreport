import { useEffect } from 'react';
import { Routes, Route } from 'react-router-dom';
import { useAuth, hasAuthParams } from 'react-oidc-context';
import Layout from './routes/Layout';
import Dedications from './routes/Dedications';
import Auth from './routes/Auth';

import AuthorizedRoute from './routes/AuthorizedRoute';

export default function App() {
  const auth = useAuth();

  useEffect(() => {
    if (!hasAuthParams() && !auth.isAuthenticated && !auth.activeNavigator && !auth.isLoading) {
      auth.signinRedirect();
    }
  }, [auth.isAuthenticated, auth.activeNavigator, auth.isLoading, auth.signinRedirect]);

  switch (auth.activeNavigator) {
    case 'signinSilent':
      return <div>Signing you in...</div>;
    case 'signoutRedirect':
      return <div>Signing you out...</div>;
  }

  if (auth.isLoading) {
    return <div>Loading...</div>;
  }

  if (auth.error) {
    return <div>Oops... {auth.error.message}</div>;
  }

  if (auth.isAuthenticated) {
    return (
      <>
        <Routes>
          <Route path="/web/v2/" element={<Layout />}>
            <Route
              path="/web/v2/dedications"
              element={
                <AuthorizedRoute>
                  <Dedications />
                </AuthorizedRoute>
              }
            />
            <Route path="/web/v2/auth" element={<Auth />} />
          </Route>
        </Routes>
      </>
    );
  } else {
    return <button onClick={() => void auth.signinRedirect()}>Log in</button>;
  }
}
