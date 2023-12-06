import AuthService from 'utils/auth';

export const isAuthenticated = () => {
  return AuthService.isAuthenticated();
};