import { useState } from 'react';

// material-ui
import {
  Grid
} from '@mui/material';

// project import

// assets
import { GiftOutlined, MessageOutlined, SettingOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';

// avatar style
const avatarSX = {
  width: 36,
  height: 36,
  fontSize: '1rem'
};

// action style
const actionSX = {
  mt: 0.75,
  ml: 1,
  top: 'auto',
  right: 'auto',
  alignSelf: 'flex-start',
  transform: 'none'
};

// sales report status
const status = [
  {
    value: 'today',
    label: 'Today'
  },
  {
    value: 'month',
    label: 'This Month'
  },
  {
    value: 'year',
    label: 'This Year'
  }
];

// ==============================|| DASHBOARD - DEFAULT ||============================== //

const DashboardDefault = () => {
  const [value, setValue] = useState('today');
  const [slot, setSlot] = useState('week');

  return (
    <>
      <MainCard title="Dashboard">
      </MainCard>
    </>
  );
};

export default DashboardDefault;
