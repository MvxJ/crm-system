import React, { useEffect } from 'react';
import * as echarts from 'echarts';
import FormatUtils from 'utils/format-utils';

const SendNotificationsStatistics = ({ messagesStatistics }) => {
  if (!Array.isArray(messagesStatistics)) {
    return <div>Error: Invalid data format</div>;
  }

  const types = [...new Set(messagesStatistics.map(stat => stat.type))];

  const dataByType = types.map(type => ({
    name: FormatUtils.getMessageBadgeObj(type).text,
    type: 'line',
    stack: 'Total',
    data: messagesStatistics
      .filter(stat => stat.type === type)
      .map(stat => stat.messageCount),
  }));

  const xAxisData = messagesStatistics.map(stat => stat.formattedDate);

  const option = {
    title: {
      text: 'Messages statistics',
      left: 'center',
    },
    tooltip: {
      trigger: 'axis',
    },
    legend: {
      data: dataByType.map(data => data.name),
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true,
    },
    toolbox: {
      feature: {
        saveAsImage: {},
      },
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: xAxisData,
    },
    yAxis: {
      type: 'value',
    },
    series: dataByType,
  };

  useEffect(() => {
    const chart = echarts.init(document.getElementById('sendNotificationsStatistics'));
    chart.setOption(option);
  }, [option]);

  return <div id="sendNotificationsStatistics" style={{ height: 400 }} />;
};

export default SendNotificationsStatistics;