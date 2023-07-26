import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { AuthProvider } from 'react-oidc-context';
import App from './App.jsx';

const oidcConfig = {
  authority: import.meta.env.VITE_OIDC_AUTHORITY,
  client_id: import.meta.env.VITE_OIDC_CLIENT_ID,
  client_secret: import.meta.env.VITE_OIDC_CLIENT_SECRET,
  redirect_uri: import.meta.env.VITE_OIDC_REDIRECT_URL,
  metadataUrl: import.meta.env.VITE_OIDC_METADATA_URL,
  response_type: import.meta.env.VITE_OIDC_RESPONSE_CODE,
  silent_redirect_uri: import.meta.env.VITE_OIDC_REDIRECT_URL,
  onSigninCallback() {
    window.history.replaceState({}, document.title, window.location.pathname);
  }
};

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <AuthProvider {...oidcConfig}>
      <BrowserRouter>
        <App />
      </BrowserRouter>
    </AuthProvider>
  </React.StrictMode>
);
