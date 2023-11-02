import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

// render - utilities
const UsersList = Loadable(lazy(() => import('pages/administration/users/UsersList')));
const SystemSettingsPage = Loadable(lazy(() => import('pages/administration/SystemSettingsPage')));

// ==============================|| MAIN ROUTING ||============================== //

const AdministrationRoutes = {
  path: '/administration',
  element: <MainLayout />,
  children: [
    {
      path: '/administration/settings',
      element: <SystemSettingsPage />
    },
    {
      path: '/administration/users',
      element: <UsersList />
    }
  ]
};

export default AdministrationRoutes;
