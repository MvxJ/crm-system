import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

// render - utilities
const VisitsList = Loadable(lazy(() => import('pages/service/serviceVisits/VisitsList')));
const RequestsList = Loadable(lazy(() => import('pages/service/serviceRequests/RequestsList')));
const DevicesList = Loadable(lazy(() => import('pages/service/devices/DevicesList')));
const ModelsList = Loadable(lazy(() => import('pages/service/models/ModelsList')));


// ==============================|| MAIN ROUTING ||============================== //

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
        path: '/service/devices',
        element: <DevicesList />
    }
  ]
};

export default AdministrationRoutes;
