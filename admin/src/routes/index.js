import { useRoutes } from 'react-router-dom';

// project import
import LoginRoutes from './LoginRoutes';
import MainRoutes from './MainRoutes';
import AdministrationRoutes from './AdministrationRoutes';
import ServiceRoutes from './ServiceRoutes';
import OfficeRoutes from './OfficeRoutes';
import ProfileRoutes from './ProfileRoutes';

// ==============================|| ROUTING RENDER ||============================== //

export default function ThemeRoutes() {
  return useRoutes([MainRoutes, AdministrationRoutes, ServiceRoutes, OfficeRoutes, ProfileRoutes, LoginRoutes]);
}
