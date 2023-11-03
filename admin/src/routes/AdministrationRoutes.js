import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

// render - utilities
const UsersList = Loadable(lazy(() => import('pages/administration/users/UsersList')));
const SystemSettingsPage = Loadable(lazy(() => import('pages/administration/SystemSettingsPage')));
const UserForm = Loadable(lazy(() => import('pages/administration/users/UserForm')));
const UserDetail = Loadable(lazy(() => import('pages/administration/users/Detail')));


const AdministrationRoutes = {
  path: '/',
  element: <MainLayout />,
  children: [
    {
      path: '/administration/settings',
      element: <SystemSettingsPage />
    },
    {
      path: '/administration/users',
      element: <UsersList />
    },
    {
      path: '/administration/users/add',
      element: <UserForm />
    },
    {
      path: '/administration/users/edit/:id',
      element: <UserForm />
    },
    {
      path: '/administration/users/detail/:id',
      element: <UserDetail />
    }
  ]
};

export default AdministrationRoutes;
