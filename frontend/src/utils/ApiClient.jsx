//import config from './config';
import jwtDecode from 'jwt-decode';
import axios from 'axios';

const apiClient = (token) => {
  const decodedAccessToken = jwtDecode(token);
  const isAccessTokenValid = decodedAccessToken.exp * 1000 > new Date().getTime();

  const initialConfig = {
    baseURL: import.meta.env.VITE_API_BASE
  };
  const client = axios.create(initialConfig);
  if (isAccessTokenValid) {
    console.log('token is valid');
    client.interceptors.request.use((config) => {
      config.headers['Authorization'] = `Bearer ${token}`;
      return config;
    });
  } else {
    console.log('your login session is not valid');
    //TODO: do something more useful here
  }
  return client;
};

export default apiClient;
