import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { DownloadOutlined, EyeOutlined } from '@ant-design/icons';

const CustomerIncoicesTab = ({ customerId }) => {
  const [data, setData] = useState([]);
  const [pageItems, setPageItems] = useState(12);
  const [loading, setLoading] = useState(false);
  const [fetchingMore, setFetchingMore] = useState(false);
  const [maxResults, setMaxResults] = useState(0);

  const fetchData = async () => {
    setLoading(true);
    setData([]);

    try {
      const response = await instance.get(`/bill/list?customerId=${customerId}`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch user invoices.",
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

        const newData = response.data.results.bills;

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
    setFetchingMore(false);
  };

  const handleFetchMore = () => {
    setPageItems(pageItems + 12);

    // Set fetchingMore to true to clear the data array
    setFetchingMore(true);

    // Fetch more data
    fetchData();
  };

  useEffect(() => {
    fetchData();
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
          <List
            dataSource={data}
            renderItem={(item) => (
              <List.Item key={item.id}>
                {item.number} 
                <div>
                    <DownloadOutlined /> Download PDF
                </div>
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

export default CustomerIncoicesTab;