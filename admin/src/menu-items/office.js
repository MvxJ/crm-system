// assets
import { FileDoneOutlined, ProfileOutlined, DollarOutlined, FileProtectOutlined } from '@ant-design/icons';

// icons
const icons = {
    FileDoneOutlined,
    ProfileOutlined,
    DollarOutlined,
    FileProtectOutlined
};

// ==============================|| MENU ITEMS - SAMPLE PAGE & DOCUMENTATION ||============================== //

const support = {
  id: 'office',
  title: 'Office',
  type: 'group',
  children: [
    {
        id: 'contracts',
        title: 'Contracts',
        type: 'item',
        url: '/office/contracts',
        icon: icons.FileProtectOutlined,
        breadcrumbs: false
    },
    {
        id: 'invoices',
        title: 'Invoices',
        type: 'item',
        url: '/office/invoices',
        icon: icons.FileDoneOutlined,
        breadcrumbs: false
    },
    {
        id: 'offers',
        title: 'Offers',
        type: 'item',
        url: '/office/offers',
        icon: icons.ProfileOutlined,
        breadcrumbs: false
    },
    {
        id: 'payments',
        title: 'Payments',
        type: 'item',
        url: '/office/payments',
        icon: icons.DollarOutlined,
        breadcrumbs: false
    }
  ]
};

export default support;
