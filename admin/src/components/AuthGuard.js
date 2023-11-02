import React from 'react';
import { useNavigate } from 'react-router-dom';
import AuthService from 'utils/auth';

const AuthGuard = ({ children }) => {
  const navigate = useNavigate();
  const isAuthenticated = AuthService.isAuthenticated();

  if (!isAuthenticated) {
    navigate('/login');
    console.log("a");

    return;
  }

  return children;
};

export default AuthGuard;