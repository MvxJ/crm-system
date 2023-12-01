import React, { useEffect, useState } from 'react';
import { Card, Col, Row } from '../../../node_modules/antd/es/index';

const AdminDashboard = ({ data }) => {
    console.log(data);

  return (
    <>
        <Row gutter={[16, 16]}>
            <Col span={12} >
                <Card>

                </Card>
            </Col>
            <Col span={12} >
                <Card>

                </Card>
            </Col>
        </Row>
        <Row gutter={[16, 16]} style={{marginTop: '15px'}}>
            <Col span={6} >
                <Card>
                    {data.usersCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card>
                    {data.customersCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card>
                    {data.activeContractsCount}
                </Card>
            </Col>
            <Col span={6} >
                <Card>
                    {data.serviceRequestCount}
                </Card>
            </Col>
        </Row>
        <Row gutter={[16, 16]} style={{marginTop: '15px'}}>
            <Col span={8} >
                <Card>

                </Card>
            </Col>
            <Col span={8} >
                <Card>

                </Card>
            </Col>
            <Col span={8} >
                <Card>

                </Card>
            </Col>
        </Row>
    </>
  );
};

export default AdminDashboard;