// material-ui
import { Typography } from '@mui/material';

import { PlusOutlined } from '@ant-design/icons';
import React from 'react';
import {
  Button,
  Cascader,
  Checkbox,
  DatePicker,
  Form,
  Input,
  InputNumber,
  Radio,
  Select,
  Slider,
  Switch,
  TreeSelect,
  Upload,
} from 'antd';

const { RangePicker } = DatePicker;
const { TextArea } = Input;

const normFile = (e) => {
  if (Array.isArray(e)) {
    return e;
  }
  return e?.fileList;
};

// project import
import MainCard from 'components/MainCard';

// ==============================|| SAMPLE PAGE ||============================== //

const SystemSettingsPage = () => (
  <>
    <MainCard title="Settings">
    </MainCard>
  </>
);

export default SystemSettingsPage;
