// project import
import Routes from 'routes';
import ThemeCustomization from 'themes';
import ScrollTop from 'components/ScrollTop';
import AuthGuard from 'components/AuthGuard';
import LoginRoutes from 'routes/LoginRoutes';
import { Outlet, Route } from '../node_modules/react-router-dom/dist/index';
import AuthLogin from 'pages/authentication/auth-forms/AuthLogin';

// ==============================|| APP - THEME, ROUTER, LOCAL  ||============================== //

const App = () => (
  <ThemeCustomization>
    <ScrollTop>
      <Routes />
    </ScrollTop>

    <Outlet />
  </ThemeCustomization>
);

export default App;
