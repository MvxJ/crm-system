// assets
import { MessageOutlined, UserOutlined } from '@ant-design/icons';

// icons
const icons = {
    MessageOutlined,
    UserOutlined
};

// ==============================|| MENU ITEMS - DASHBOARD ||============================== //

const dashboard = {
  id: 'main',
  title: 'Main',
  type: 'group',
  children: [
    {
      id: 'customers',
      title: 'Customers',
      type: 'item',
      url: '/customers',
      icon: icons.UserOutlined,
      breadcrumbs: false
    },
    {
        id: 'messages',
        title: 'Messages',
        type: 'item',
        url: '/messages',
        icon: icons.MessageOutlined,
        breadcrumbs: false
      }
  ]
};

export default dashboard;
