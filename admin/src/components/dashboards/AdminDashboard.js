import React, { useEffect, useState } from 'react';
import { Card, Col, Row, Typography } from '../../../node_modules/antd/es/index';
import AuthCustomersChart from './charts/AuthCustomersChart';
import TwofaUsersChart from './charts/TwofaUsersChart';
import { TeamOutlined, FileProtectOutlined, IssuesCloseOutlined } from '@ant-design/icons';
import CustomersNotificationChart from './charts/CustomersNotificationCharts';
import './Dashboard.css';
import ContractsPieChart from './charts/ContractsPieChart';
import SendNotificationsStatistics from './charts/SendNotificationsStatistics';

const AdminDashboard = ({ data }) => {
    console.log(data);

  return (
    <>
        <Row gutter={[16, 16]}>
            <Col span={12} >
                <Card>
                    <AuthCustomersChart stats={data.customerConfirmedAccountStatistics} />
                </Card>
            </Col>
            <Col span={12} >
                <Card>
                    <SendNotificationsStatistics stats={data.messagesStatistics} />
                </Card>
            </Col>
        </Row>
        <Row gutter={[16, 16]} style={{marginTop: '15px'}}>
            <Col span={6} >
                <Card className="countContainer">
                    <Typography className="colHeader">
                        <TeamOutlined /> Active Users
                    </Typography>
                    {data.usersCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card className="countContainer">
                    <Typography className="colHeader">
                        <TeamOutlined /> Registered customers
                    </Typography>
                    {data.customersCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card className="countContainer">
                    <Typography className="colHeader">
                        <FileProtectOutlined /> Active contracts
                    </Typography>
                    {data.activeContractsCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card className="countContainer">
                    <Typography className="colHeader">
                        <IssuesCloseOutlined /> Service Requests
                    </Typography>
                    {data.serviceRequestCount}
                </Card>
            </Col>
        </Row>
        <Row gutter={[16, 16]} style={{marginTop: '15px'}}>
            <Col span={8} >
                <Card>
                    <CustomersNotificationChart stats={data.customerNotificationStatistics[0]} />
                </Card>
            </Col>
            <Col span={8} >
                <Card>
                    <TwofaUsersChart stats={data.customer2faStatistics} />
                </Card>
            </Col>
            <Col span={8} >
                <Card>
                    <ContractsPieChart stats={data.contractsStatistics} />
                </Card>
            </Col>
        </Row>
    </>
  );
};

export default AdminDashboard;