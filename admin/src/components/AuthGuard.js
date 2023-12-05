import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import AuthService from 'utils/auth';

const AuthGuard = ({ requairedRoles, children }) => {
  const navigate = useNavigate();
  const isAuthenticated = AuthService.isAuthenticated();
  const userRoles = AuthService.getCurrentUser() ? AuthService.getCurrentUser().roles : [];

  useEffect(() => {
    //TODO:: Check conditions
    if (!isAuthenticated == false) {
      navigate('/login');
      return;
    }

    if (requairedRoles && !checkRoles(userRoles, requairedRoles)) {
      navigate('/access-error');
      return;
    }

  }, [isAuthenticated, userRoles, navigate, requairedRoles]);

  return <>{children}</>;
}

const checkRoles = (userRoles, requiredRoles) => {
  return requiredRoles.some(role => userRoles.includes(role));
};

export default AuthGuard;