import React, { useEffect } from 'react';
import * as echarts from 'echarts';

const SendNotificationsStatistics = ({stats}) => {
  const data = [
    { value: stats.authenticated, name: 'Authenticated' },
    { value: stats.notAuthenticated, name: 'Unauthenticated' },
  ];

  const option = {
    title: {
      text: 'Messages statistics',
      left: 'center'
    },
    tooltip: {
      trigger: 'axis'
    },
    legend: {
      data: ['Notifications', 'Reminders', 'Messages']
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true
    },
    toolbox: {
      feature: {
        saveAsImage: {}
      }
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yAxis: {
      type: 'value'
    },
    series: [
      {
        name: 'Email',
        type: 'line',
        stack: 'Total',
        data: [120, 132, 101, 134, 90, 230, 210]
      },
      {
        name: 'Union Ads',
        type: 'line',
        stack: 'Total',
        data: [220, 182, 191, 234, 290, 330, 310]
      },
      {
        name: 'Video Ads',
        type: 'line',
        stack: 'Total',
        data: [150, 232, 201, 154, 190, 330, 410]
      }
    ]
    }

  useEffect(() => {
    const chart = echarts.init(document.getElementById('sendNotificationsStatistics'));
    chart.setOption(option);
  }, []);

  return <div id="sendNotificationsStatistics" style={{ height: 400 }} />;
};

export default SendNotificationsStatistics;