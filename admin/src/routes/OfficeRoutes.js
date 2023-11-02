import { lazy } from 'react';

// project import
import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

// render - utilities
const PaymentsList = Loadable(lazy(() => import('pages/office/payments/PaymentsList')));
const ContractsList = Loadable(lazy(() => import('pages/office/contracts/ContractsList')));
const OffersList = Loadable(lazy(() => import('pages/office/offers/OffersList')));
const InvoicesList = Loadable(lazy(() => import('pages/office/invoices/InvoicesList')));


// ==============================|| MAIN ROUTING ||============================== //

const AdministrationRoutes = {
  path: '/office',
  element: <MainLayout />,
  children: [
    {
        path: '/office/payments',
        element: <PaymentsList />
    },
    {
        path: '/office/contracts',
        element: <ContractsList />
    },
    {
        path: '/office/offers',
        element: <OffersList />
    },
    {
        path: '/office/invoices',
        element: <InvoicesList />
    }
  ]
};

export default AdministrationRoutes;
