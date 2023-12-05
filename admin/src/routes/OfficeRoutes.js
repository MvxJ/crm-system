import { lazy } from 'react';

import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';
import AuthService from 'utils/auth';
import AuthGuard from 'components/AuthGuard';

const PaymentsList = Loadable(lazy(() => import('pages/office/payments/PaymentsList')));
const PaymentDetail = Loadable(lazy(() => import('pages/office/payments/PaymentDetail')));
const PaymentForm = Loadable(lazy(() => import('pages/office/payments/PaymentForm')));
const ContractsList = Loadable(lazy(() => import('pages/office/contracts/ContractsList')));
const ContractDetail = Loadable(lazy(() => import('pages/office/contracts/ContractDetail')));
const ContractForm = Loadable(lazy(() => import('pages/office/contracts/ContractForm')));
const OffersList = Loadable(lazy(() => import('pages/office/offers/OffersList')));
const OfferDetail = Loadable(lazy(() => import('pages/office/offers/OfferDetail')));
const OfferForm = Loadable(lazy(() => import('pages/office/offers/OfferForm')));
const InvoicesList = Loadable(lazy(() => import('pages/office/invoices/InvoicesList')));
const InvoiceDetail = Loadable(lazy(() => import('pages/office/invoices/InvoiceDetail')));
const InvoiceForm = Loadable(lazy(() => import('pages/office/invoices/InvoiceForm')));

const userRoles = AuthService.getCurrentUser().roles;
var routes = [
{
  path: '/office/contracts',
  element: <ContractsList />
},
{
  path: '/office/contracts/detail/:id',
  element: <ContractDetail />
},
{
  path: '/office/contracts/add',
  element: <ContractForm />
},
{
  path: '/office/contracts/edit/:id',
  element: <ContractForm />
},
{
  path: '/office/offers',
  element: <OffersList />
},
{
  path: '/office/offers/detail/:id',
  element: <OfferDetail />
},
{
  path: '/office/offers/add',
  element: <OfferForm />
},
{
  path: '/office/offers/edit/:id',
  element: <OfferForm />
}
]

const paymentRoutes = [
  {
    path: '/office/payments',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><PaymentsList /></AuthGuard>)
  },
  {
    path: '/office/payments/detail/:id',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><PaymentDetail /></AuthGuard>)
  },
  {
    path: '/office/payments/add',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><PaymentForm /></AuthGuard>)
  },
  {
    path: '/office/payments/edit/:id',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><PaymentForm /></AuthGuard>)
  }
]

const invoiceRoutes = [
  {
    path: '/office/invoices',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><InvoicesList /></AuthGuard>)
  },
  {
    path: '/office/invoices/detail/:id',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><InvoiceDetail /></AuthGuard>)
  },
  {
    path: '/office/invoices/edit/:id',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><InvoiceForm /></AuthGuard>)
  },
  {
    path: '/office/invoices/add',
    element: (<AuthGuard requairedRoles={['Administrator', 'Księgowość']}><InvoiceForm /></AuthGuard>)
  }
]

if (userRoles.includes('Administrator') || userRoles.includes('Księgowość')) {
  routes = [...routes, ...invoiceRoutes];
  routes = [...routes, ...paymentRoutes];
}

const AdministrationRoutes = {
  path: '/office',
  element: <MainLayout />,
  children: routes
};

export default AdministrationRoutes;
