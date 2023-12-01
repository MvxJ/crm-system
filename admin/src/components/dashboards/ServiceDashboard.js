import React, { useEffect, useState } from 'react';
import { Card, Col, Row } from '../../../node_modules/antd/es/index';

const ServiceDashboard = ({ data }) => {
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

                </Card>
            </Col>
            <Col span={6} >
                <Card>

                </Card>
            </Col>
            <Col span={6} >
                <Card>

                </Card>
            </Col>
            <Col span={6} >
                <Card>

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

export default ServiceDashboard;