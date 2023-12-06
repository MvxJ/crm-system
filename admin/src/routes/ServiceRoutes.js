import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';
import AuthGuard from 'components/AuthGuard';

// render - utilities
const VisitsList = Loadable(lazy(() => import('pages/service/serviceVisits/VisitsList')));
const VisitForm = Loadable(lazy(() => import('pages/service/serviceVisits/VisitForm')));
const VisitDetail = Loadable(lazy(() => import('pages/service/serviceVisits/VisitDetail')));
const RequestsList = Loadable(lazy(() => import('pages/service/serviceRequests/RequestsList')));
const ServiceRequestDetail = Loadable(lazy(() => import('pages/service/serviceRequests/ServiceRequestDetail')));
const ServiceRequestForm = Loadable(lazy(() => import('pages/service/serviceRequests/ServiceRequestForm')));
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
      path: '/service/visits/detail/:id',
      element: <VisitDetail />
    },
    {
      path: '/service/visits/edit/:id',
      element: <VisitForm />
    },
    {
      path: '/service/visits/add',
      element: <VisitForm />
    },
    {
      path: '/service/requests',
      element: <RequestsList />
    },
    {
      path: '/service/requests/detail/:id',
      element: <ServiceRequestDetail />
    },
    {
      path: '/service/requests/edit/:id',
      element: <ServiceRequestForm />
    },
    {
      path: '/service/requests/add',
      element: <ServiceRequestForm />
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
      element: (<AuthGuard requairedRoles={['Administrator', 'Serwis']}><ModelForm /></AuthGuard>)
    },
    {
      path: '/service/models/edit/:id',
      element: (<AuthGuard requairedRoles={['Administrator', 'Serwis']}><ModelForm /></AuthGuard>)
    },
    {
      path: '/service/devices',
      element: <DevicesList />
    },
    {
      path: '/service/devices/add',
      element: (<AuthGuard requairedRoles={['Administrator', 'Serwis']}><DeviceForm /></AuthGuard>)
    },
    {
      path: '/service/devices/edit/:id',
      element: (<AuthGuard requairedRoles={['Administrator', 'Serwis']}><DeviceForm /></AuthGuard>)
    },
    {
      path: '/service/devices/details/:id',
      element: <DeviceDetail />
    }
  ]
};

export default AdministrationRoutes;
