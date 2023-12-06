import React, { useEffect, useState } from 'react';
import * as echarts from 'echarts';
import FormatUtils from 'utils/format-utils';

const ContractsPieChart = ({stats}) => {
  useEffect(() => {
    const dataNew = [];

    stats.forEach(element => {
      dataNew.push({ value: element.contractCount, name: FormatUtils.getContractBadgeDetails(element.status).text })
    });

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
          data: dataNew,
          color: ['#95de64', '#f5222d']
        },
      ],
    };

    const chart = echarts.init(document.getElementById('contractsPieChart'));
    chart.setOption(option);
  }, []);

  return <div id="contractsPieChart" style={{ height: 400 }} />;
};

export default ContractsPieChart;