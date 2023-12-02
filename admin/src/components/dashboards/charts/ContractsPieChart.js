import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const ContractsPieChart = ({stats}) => {
  const data = [
    { value: stats.authenticated, name: 'Authenticated' },
    { value: stats.notAuthenticated, name: 'Unauthenticated' },
  ];

  const option = {
    title: {
      text: 'Contracts statistics',
      left: 'center',
    },
    series: [
      {
        name: 'Contracts',
        type: 'pie',
        radius: '55%',
        data: data,
      },
    ],
  };

  useEffect(() => {
    const chart = echarts.init(document.getElementById('contractsPieChart'));
    chart.setOption(option);
  }, []);

  return <div id="contractsPieChart" style={{ height: 400 }} />;
};

export default ContractsPieChart;