import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

// render - utilities
const VisitsList = Loadable(lazy(() => import('pages/service/serviceVisits/VisitsList')));
const RequestsList = Loadable(lazy(() => import('pages/service/serviceRequests/RequestsList')));
const DevicesList = Loadable(lazy(() => import('pages/service/devices/DevicesList')));
const DeviceDetail = Loadable(lazy(() => import('pages/service/devices/DeviceDetail')));
const DeviceForm = Loadable(lazy(() => import('pages/service/devices/DeviceForm')));
const ModelsList = Loadable(lazy(() => import('pages/service/models/ModelsList')));
const ModelDetail = Loadable(lazy(() => import('pages/service/models/ModelDetail')));
const ModelForm = Loadable(lazy(() => import('pages/service/models/ModelForm')));

const AdministrationRoutes = {
  path: '/service',
  element: <MainLayout />,
  children: [
    {
        path: '/service/visits',
        element: <VisitsList />
    },
    {
        path: '/service/requests',
        element: <RequestsList />
    },
    {
        path: '/service/models',
        element: <ModelsList />
    },
    {
      path: '/service/models/detail/:id',
      element: <ModelDetail />
    },
    {
      path: '/service/models/add',
      element: <ModelForm />
    },
    {
      path: '/service/models/edit/:id',
      element: <ModelForm />
    },
    {
        path: '/service/devices',
        element: <DevicesList />
    },
    {
      path: '/service/devices/add',
      element: <DeviceForm />
    },
    {
      path: '/service/devices/edit/:id',
      element: <DeviceForm />
    },
    {
      path: '/service/devices/details/:id',
      element: <DeviceDetail />
    }
  ]
};

export default AdministrationRoutes;
