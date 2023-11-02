import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

const DashboardDefault = Loadable(lazy(() => import('pages/dashboard')));
const CustomersList = Loadable(lazy(() => import('pages/main/customers/CustomersList')));
const MessagesList = Loadable(lazy(() => import('pages/main/messages/MessagesList')));


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
      path: '/messages',
      element: <MessagesList />
    }
  ]
};

export default MainRoutes;
