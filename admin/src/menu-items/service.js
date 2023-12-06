// assets
import { CalendarOutlined, IssuesCloseOutlined, ClusterOutlined, UnorderedListOutlined } from '@ant-design/icons';

// icons
const icons = {
    IssuesCloseOutlined,
    CalendarOutlined,
    ClusterOutlined,
    UnorderedListOutlined
};

// ==============================|| MENU ITEMS - SAMPLE PAGE & DOCUMENTATION ||============================== //

const support = {
  id: 'service',
  title: 'Service',
  type: 'group',
  children: [
    {
        id: 'service-requests',
        title: 'Service Requests',
        type: 'item',
        url: '/service/requests',
        icon: icons.IssuesCloseOutlined,
        breadcrumbs: false
    },
    {
        id: 'service-visits',
        title: 'Service Visits',
        type: 'item',
        url: '/service/visits',
        icon: icons.CalendarOutlined,
        breadcrumbs: false
    },
    {
        id: 'devices',
        title: 'Devices',
        type: 'item',
        url: '/service/devices',
        icon: icons.ClusterOutlined,
        breadcrumbs: false
    },
    {
        id: 'models',
        title: 'Models',
        type: 'item',
        url: '/service/models',
        icon: icons.UnorderedListOutlined,
        breadcrumbs: false
    }
  ]
};

export default support;
