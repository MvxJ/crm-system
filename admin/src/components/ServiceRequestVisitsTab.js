import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { MoreOutlined, EyeOutlined, ClockCircleOutlined } from '@ant-design/icons';
import './TabStyles.css';
import { useNavigate } from '../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';

const ServiceRequestVisitsTab = ({ serviceRequestId }) => {
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
      const response = await instance.get(`/service/visit/list?serviceRequestId=${serviceRequestId}&items=${items}&order=DESC&orderBy=id`);

      if (response.status !== 200) {
        notification.error({
          message: "Can't fetch service visits.",
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

      const newData = response.data.serviceVisits ? response.data.serviceVisits.serviceVisits : [];

      setMaxResults(response.data.serviceVisits ? response.data.serviceVisits.maxResults : 0);
      setData(newData);
    } catch (error) {
      notification.error({
        message: "Can't fetch service visits.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }

    setLoading(false);
  };

  const handleServiceRequestDetail = () => {
    navigate(`/service/visits/detail/${currentId}`);
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
  }, [serviceRequestId]);

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
                        <Col style={{marginRight: '5px'}}>
                            <div className='badge' style={{backgroundColor: FormatUtils.getServiceVisitBadge(item.isFinished, item.cancelled).color}}>
                                {FormatUtils.getServiceVisitBadge(item.isFinished, item.cancelled).text}
                            </div>
                        </Col>
                        <Col>
                          <b>Date:</b> {FormatUtils.formatDateWithTime(item.date.date)} &nbsp;
                        </Col>
                        <Col>
                            <ClockCircleOutlined /> <b>Start Time: </b>{FormatUtils.formatDateWithTime(item.start.date)} &nbsp;
                        </Col>
                        <Col>
                            <ClockCircleOutlined /> <b>End Time: </b>{FormatUtils.formatDateWithTime(item.end.date)} &nbsp;
                        </Col>
                        {item.user ?
                          <Col>
                            <b>Techincian:</b> {item.user.username} &nbsp;
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

export default ServiceRequestVisitsTab;