import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout/index';
import NotFound from 'components/NotFound';
import AccessError from 'components/AccessError';

// render - login
const AuthLogin = Loadable(lazy(() => import('pages/authentication/Login')));
// ==============================|| AUTH ROUTING ||============================== //

const HelperRoutes = {
  path: '/',
  element: <MainLayout />,
  children: [
    {
      path: 'access-error',
      element: <AccessError />
    },
    {
        path: '*',
        element: <NotFound />
    }
  ]
};

export default HelperRoutes;
