import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const AuthCustomersChart = ({stats}) => {
  const data = [
    { value: stats.authenticated, name: 'Authenticated' },
    { value: stats.notAuthenticated, name: 'Unauthenticated' },
  ];

  const option = {
    title: {
      text: 'Authenticated/Unauthenticated Customers',
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
    const chart = echarts.init(document.getElementById('authCustomersChart'));
    chart.setOption(option);
  }, []);

  return <div id="authCustomersChart" style={{ height: 400 }} />;
};

export default AuthCustomersChart;