import { useRoutes } from 'react-router-dom';

// project import
import LoginRoutes from './LoginRoutes';
import MainRoutes from './MainRoutes';
import AdministrationRoutes from './AdministrationRoutes';
import ServiceRoutes from './ServiceRoutes';
import OfficeRoutes from './OfficeRoutes';
import ProfileRoutes from './ProfileRoutes';
import HelperRoutes from './HelperRoutes';
import AuthService from 'utils/auth';

// ==============================|| ROUTING RENDER ||============================== //
const userRoles = AuthService.getCurrentUser() ? AuthService.getCurrentUser().roles : [];
var routes = [MainRoutes, ServiceRoutes, OfficeRoutes, ProfileRoutes, LoginRoutes, HelperRoutes]

if (userRoles.includes('Administrator')) {
  routes.push(AdministrationRoutes)
}

export default function ThemeRoutes() {
  return useRoutes(routes);
}
