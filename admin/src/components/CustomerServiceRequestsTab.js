import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { MoreOutlined, EyeOutlined } from '@ant-design/icons';
import './TabStyles.css';
import { useNavigate } from '../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';

const CustomerServiceRequestsTab = ({ customerId }) => {
  const [data, setData] = useState([]);
  const [pageItems, setPageItems] = useState(12);
  const [loading, setLoading] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [currentId, setCurrentId] = useState(null);
  const [maxResults, setMaxResults] = useState(0);
  const navigate = useNavigate();

  const fetchData = async (items) => {
    setLoading(true);
    setData([]);

    try {
      const response = await instance.get(`/service-requests/list?customerId=${customerId}&items=${items}&order=DESC&orderBy=id`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch user messages.",
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

      const newData = response.data.results ? response.data.results.serviceRequests : [];

      setMaxResults(response.data.results ? response.data.results.maxResults : 0);
      setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch user service requests.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }

    setLoading(false);
  };

  const handleServiceRequestDetail = () => {
    navigate(`/service/requests/details/${currentId}`);
  }

  const handleClick = (event, item) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentId(item.id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
  };

  const handleFetchMore = () => {
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
          <Col span={24} style={{ textAlign: 'center' }}>
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
                <div style={{ width: '100%' }}>
                  <Row style={{ display: 'flex', justifyContent: 'center' }}>
                    <Col span={23}>
                      <Row>
                        <Col>
                          <div className='status-badge' style={{ backgroundColor: FormatUtils.getServiceRequestStatusBadge(item.status).color }}>
                            {FormatUtils.getServiceRequestStatusBadge(item.status).text}
                          </div>
                        </Col>
                        <Col>
                          <b>Created at:</b> {FormatUtils.formatDateWithTime(item.createdDate.date)} &nbsp;

                        </Col>
                        <Col>
                          <b>Contract number:</b> {item.contract.number} &nbsp;
                        </Col>
                        {item.user ?
                          <Col>
                            <b>Techincian:</b> {item.user.email} &nbsp;
                          </Col>
                          : null
                        }
                      </Row>
                    </Col>
                    <Col span={1} style={{ textAlign: 'right' }}>
                      <div className="actionColumn">
                        <MoreOutlined onClick={(event) => handleClick(event, item)} />
                        <Menu
                          anchorEl={anchorEl}
                          open={Boolean(anchorEl)}
                          onClose={handleClose}
                          onClick={handleClose}
                          PaperProps={{
                            style: {
                              boxShadow: '0px 2px 4px rgba(0, 0, 0, 0.1)',
                            },
                          }}
                        >
                          <MenuItem onClick={handleServiceRequestDetail}>
                            <Link className="text-decoration-none">
                              <EyeOutlined /> Details
                            </Link>
                          </MenuItem>
                        </Menu>
                      </div>
                    </Col>
                  </Row>
                </div>
              </List.Item>
            )}
          />
          {
            data.length < maxResults ?
              <div style={{ textAlign: 'center', width: '100%' }}>
                <a onClick={handleFetchMore}>Fetch More Data</a>
              </div>
              : null
          }
        </div>
      )}
    </>
  );
};

export default CustomerServiceRequestsTab;