import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { EyeOutlined } from '@ant-design/icons';
import './TabStyles.css';

const CustomerDevicesTab = ({ customerId }) => {
  const [data, setData] = useState([]);
  const [pageItems, setPageItems] = useState(12);
  const [loading, setLoading] = useState(false);
  const [fetchingMore, setFetchingMore] = useState(false);
  const [maxResults, setMaxResults] = useState(0);

  const fetchData = async (items) => {
    setLoading(true);
    setData([]);

    try {
      const response = await instance.get(`/devices/list?customerId=${customerId}&items=${items}&order=DESC&orderBy=id`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch user messages.",
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

        const newData = response.data.results.devices;

        setMaxResults(response.data.results.maxResults);
        setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch user invoices.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }

    setLoading(false);
  };

  const handleFetchMore = async () => {
    const maxResults = 12 + pageItems;
    setPageItems(maxResults);

    fetchData(maxResults);
  };

  useEffect(() => {
    fetchData(pageItems);
  }, [customerId]);

  return (
    <>
      {loading ? (
        <Row>
            <Col span={24} style={{textAlign: 'center'}}>
                <Spin />
            </Col>
        </Row>
      ) : (
        <div>
            <Row className="number-of-showing">
                Showing {data.length} of {maxResults}
            </Row>
          <List
            dataSource={data}
            renderItem={(item) => (
              <List.Item key={item.id}>
                {item.macAddress} 
                <div>
                    <EyeOutlined />Details
                </div>
            </List.Item>
            )}
          />
          {
            data.length < maxResults ?
            <div style={{textAlign: 'center', width: '100%'}}>
                <a onClick={handleFetchMore}>Fetch More Data</a>
            </div>
            : null
          }
        </div>
      )}
    </>
  );
};

export default CustomerDevicesTab;