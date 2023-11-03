// project import
import Routes from 'routes';
import ThemeCustomization from 'themes';
import ScrollTop from 'components/ScrollTop';
import { Outlet, Route } from '../node_modules/react-router-dom/dist/index';

// ==============================|| APP - THEME, ROUTER, LOCAL  ||============================== //

const App = () => (
  <ThemeCustomization>
    <ScrollTop>
      <Routes />
      <Outlet />
    </ScrollTop>
  </ThemeCustomization>
);

export default App;
