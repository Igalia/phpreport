import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from 'react-oidc-context';

//TODO: At some point, we'll need to check permissions in addition to authentication

const AuthorizedRoute = ({ children }) => {
  const auth = useAuth();
  const location = useLocation();

  return auth.isAuthenticated ? (
    children
  ) : (
    <Navigate to="/web/login.php" />
    //TODO: when the app is only using react, this would be the syntax
    //<Navigate to="" replace state={{ path: location.pathname }} />
  );
};

export default AuthorizedRoute;
