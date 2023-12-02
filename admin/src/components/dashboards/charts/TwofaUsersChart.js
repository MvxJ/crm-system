import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const TwofaUsersChart = ({stats}) => {
  const data = [
    { value: stats.enabled2FA, name: '2fa Enabled' },
    { value: stats.disabled2FA, name: '2fa Disabled' },
  ];

  const option = {
    title: {
      text: 'Customers 2fa usage chart',
      left: 'center',
    },
    series: [
      {
        name: 'Customers',
        type: 'pie',
        radius: '55%',
        data: data,
      },
    ],
  };

  useEffect(() => {
    const chart = echarts.init(document.getElementById('2faUsersChart'));
    chart.setOption(option);
  }, []);

  return <div id="2faUsersChart" style={{ height: 400 }} />;
};

export default TwofaUsersChart;