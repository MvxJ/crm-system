import { lazy } from 'react';

import Loadable from 'components/Loadable';
import MainLayout from 'layout/MainLayout';

const ProfileSettings = Loadable(lazy(() => import('pages/profile/Settigns')));
const EditProfile = Loadable(lazy(() => import('pages/profile/Edit')));


const ProfileRoutes = {
  path: '/',
  element: <MainLayout />,
  children: [
    {
        path: '/profile/edit',
        element: <EditProfile />
    },
    {
        path: '/profile/settings',
        element: <ProfileSettings />
    }
  ]
};

export default ProfileRoutes;
