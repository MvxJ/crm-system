import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';
import AuthGuard from 'components/AuthGuard';

const DashboardDefault = Loadable(lazy(() => import('pages/dashboard')));
const CustomersList = Loadable(lazy(() => import('pages/main/customers/CustomersList')));
const CustomerDetails = Loadable(lazy(() => import('pages/main/customers/CustomerDetails')));
const CustomerForm = Loadable(lazy(() => import('pages/main/customers/CustomerForm')))
const MessagesList = Loadable(lazy(() => import('pages/main/messages/MessagesList')));
const MessageForm = Loadable(lazy(() => import('pages/main/messages/MessageForm')));
const MessageDetails = Loadable(lazy(() => import('pages/main/messages/MessageDetails')));

const MainRoutes = {
  path: '/',
  element: <MainLayout />,
  children: [
    {
      path: '/',
      element: <DashboardDefault />
    },
    {
      path: '/customers',
      element: <CustomersList />
    },
    {
      path: '/customers/add',
      element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><CustomerForm /></AuthGuard>)
    },
    {
      path: '/customers/detail/:id',
      element: <CustomerDetails />
    },
    {
      path: '/messages',
      element: <MessagesList />
    },
    {
      path: '/messages/create',
      element: <MessageForm />
    },
    {
      path: '/messages/detail/:id',
      element: <MessageDetails />
    }
  ]
};

export default MainRoutes;
