import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const CustomersNotificationChart = ({ stats }) => {
  const option = {
    title: {
      text: 'Customers notifications statistics',
      left: 'center',
    },
    xAxis: {
      type: 'category',
      data: ['Email & Sms', 'Only Sms', 'Only Email', 'None'],
    },
    yAxis: {
      type: 'value',
    },
    series: [
      {
        name: 'Customers',
        data: [
          parseInt(stats.bothNotifications),
          parseInt(stats.smsOnly),
          parseInt(stats.emailOnly),
          parseInt(stats.noneNotifications),
        ],
        type: 'bar',
        itemStyle: {
          color: function (params) {
            const colorArray = ['#4CAF50', '#FFC107', '#2196F3', '#FF5722'];
            return colorArray[params.dataIndex];
          },
        },
      },
    ],
  };

  useEffect(() => {
    const chart = echarts.init(document.getElementById('customersNotifcationsChart'));
    chart.setOption(option);
  }, [option]);

  return <div id="customersNotifcationsChart" style={{ height: 400 }} />;
};

export default CustomersNotificationChart;