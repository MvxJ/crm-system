import React, { useEffect, useState } from 'react';
import { List, Spin } from 'antd';
import instance from 'utils/api';
import { notification } from 'antd';
import { Col, Row } from '../../node_modules/antd/es/index';
import { MoreOutlined, EyeOutlined } from '@ant-design/icons';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import './TabStyles.css';
import FormatUtils from 'utils/format-utils';
import { useNavigate } from '../../node_modules/react-router-dom/dist/index';
import { current } from '../../node_modules/@reduxjs/toolkit/dist/index';

const CustomerContractsTab = ({ customerId }) => {
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
            const response = await instance.get(`/contracts/list?customerId=${customerId}&items=${items}&order=DESC&orderBy=id`);

            if (response.status !== 200) {
                notification.error({
                    message: "Can't fetch user contracts.",
                    type: 'error',
                    placement: 'bottomRight'
                });
                return;
            }

            const newData = response.data.results.contracts;

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

    const handleFetchMore = () => {
        const maxItems = pageItems + 12;
        setPageItems(maxItems);

        fetchData(maxItems);
    };

    const handleClick = (event, item) => {
        event.stopPropagation();
        setAnchorEl(event.currentTarget);
        setCurrentId(item.id);
    };

    const handleClose = () => {
        setAnchorEl(null);
        setCurrentId(null);
    };

    const handleNavigateConractDetail = () => {
        navigate(`/contracts/detail/${c}`)
    }

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
                <div>            <Row className="number-of-showing">
                    Showing {data.length} of {maxResults}
                </Row>
                    <List
                        dataSource={data}
                        renderItem={(item) => (
                            <List.Item key={item.id}>
                                <Row>
                                    <Col spam={22}>
                                        <Row>
                                            <Col>
                                                <div className='status-badge' style={{ backgroundColor: FormatUtils.getContractBadgeDetails(item.status).color }}>
                                                    {FormatUtils.getContractBadgeDetails(item.status).text}
                                                </div>
                                            </Col>
                                            <Col>
                                                <b>Created at:</b> {FormatUtils.formatDateWithTime(item.createdAt.date)} &nbsp;

                                            </Col>
                                            <Col>
                                                <b>Contract number:</b> {item.contractNumber} &nbsp;
                                            </Col>
                                            <Col>
                                                <b>Start service date:</b> {FormatUtils.formatDateWithTime(item.startDate.date)} &nbsp;
                                            </Col>
                                        </Row>
                                    </Col>
                                    <Col span={2}>
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
                                                <MenuItem onClick={handleNavigateConractDetail}>
                                                    <Link className="text-decoration-none">
                                                        <EyeOutlined /> Contract Details
                                                    </Link>
                                                </MenuItem>
                                            </Menu>
                                        </div>
                                    </Col>
                                </Row>
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

export default CustomerContractsTab;