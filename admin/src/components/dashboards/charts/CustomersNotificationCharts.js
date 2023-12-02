import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const CustomersNotificationChart = ({stats}) => {
  const option = {
    title: {
      text: 'Customers notifications statistics',
      left: 'center',
    },
    xAxis: {
      type: 'category',
      data: ['Email & Sms', 'Only Sms', 'Only Email', 'None']
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name: 'Customers',
        data: [parseInt(stats.bothNotifications), parseInt(stats.smsONly), parseInt(stats.emailOnly), parseInt(stats.noneNotifications)],
        type: 'bar'
      }
    ]
  };

  useEffect(() => {
    const chart = echarts.init(document.getElementById('customersNotifcationsChart'));
    chart.setOption(option);
  }, []);

  return <div id="customersNotifcationsChart" style={{ height: 400 }} />;
};

export default CustomersNotificationChart;