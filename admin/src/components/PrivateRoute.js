import React from 'react';
import { useNavigate } from 'react-router-dom';
import AuthService from 'utils/auth';

const PrivateRoute = ({ children }) => {
  const navigate = useNavigate();
  const isLoggedIn = AuthService.isAuthenticated();

  if (!isLoggedIn) {
    navigate('/login');
    return null;
  }

  return children;
};

export default PrivateRoute;