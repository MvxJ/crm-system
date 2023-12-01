import { useEffect, useState } from 'react';
import { GiftOutlined, MessageOutlined, SettingOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import AdminDashboard from 'components/dashboards/AdminDashboard';
import { Spin, notification } from '../../../node_modules/antd/es/index';
import instance from 'utils/api';
import OfficeDashboard from 'components/dashboards/OfficeDashboard';
import ServiceDashboard from 'components/dashboards/ServiceDashboard';
import DefaultDashboard from 'components/dashboards/DefaultDashboard';
import ErrorDashboard from 'components/dashboards/ErrorDashboard';

const DashboardDefault = () => {
  const [analyticData, setAnalyticData] = useState([]);
  const [dashboardType, setDashboardType] = useState('');
  const [loading, setLoading] = useState(false);

  const fetchData = async () => {
    try {
        const response = await instance.get('/statistics');

        if (response.status != 200) {
          setLoading(false);

          notification.error({
            message: "Can't fetch analytic data.",
            type: "error",
            placement: "bottomRight"
          });

          return;
        }

        setDashboardType(response.data.results.assignedStatisticForRole)
        setAnalyticData(response.data.results.analytic)
        setLoading(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't fetch analytic data.",
        type: "error",
        descrption: e.message,
        placement: "bottomRight"
      });
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <>
      <Spin style={{minHeight: '80vh'}} spinning={loading}>
        { dashboardType == 'ROLE_ADMIN' ? <AdminDashboard data={analyticData} /> : null }
        { dashboardType == 'ROLE_ACCOUNTMENT' ? <OfficeDashboard data={analyticData} /> : null }
        { dashboardType == 'ROLE_SERVICE' ? <ServiceDashboard data={analyticData} /> : null }
        { dashboardType == 'ROLE_ACCESS_ADMIN_PANEL' ? <DefaultDashboard data={analyticData} /> : null}
        { dashboardType == '' ? <ErrorDashboard /> : null }
      </Spin>
    </>
  );
};

export default DashboardDefault;
