import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';
import AuthGuard from 'components/AuthGuard';

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
      element: (<AuthGuard requairedRoles={['Administrator']}><SystemSettingsPage /></AuthGuard>)
    },
    {
      path: '/administration/users',
      element: (<AuthGuard requairedRoles={['Administrator']}><UsersList /></AuthGuard>)
    },
    {
      path: '/administration/users/add',
      element: (<AuthGuard requairedRoles={['Administrator']}><UserForm /></AuthGuard>)
    },
    {
      path: '/administration/users/edit/:id',
      element: (<AuthGuard requairedRoles={['Administrator']}><UserForm /></AuthGuard>)
    },
    {
      path: '/administration/users/detail/:id',
      element: (<AuthGuard requairedRoles={['Administrator']}><UserDetail /></AuthGuard>)
    }
  ]
};

export default AdministrationRoutes;
