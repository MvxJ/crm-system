// assets
import { UserOutlined, SettingOutlined } from '@ant-design/icons';

// icons
const icons = {
    UserOutlined,
    SettingOutlined
};

// ==============================|| MENU ITEMS - SAMPLE PAGE & DOCUMENTATION ||============================== //

const support = {
  id: 'administration',
  title: 'Administration',
  type: 'group',
  children: [
    {
        id: 'users',
        title: 'Users',
        type: 'item',
        url: '/administration/users',
        icon: icons.UserOutlined,
        breadcrumbs: false
    },
    {
        id: 'settings',
        title: 'Settings',
        type: 'item',
        url: '/administration/settings',
        icon: icons.SettingOutlined,
        breadcrumbs: false
    }
  ]
};

export default support;
