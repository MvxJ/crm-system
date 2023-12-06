import React, { useState } from 'react';
import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Row, Tabs } from '../../../../node_modules/antd/es/index';
import './Customers.css';
import { SettingOutlined, HomeOutlined, UserOutlined } from '@ant-design/icons';
import CustomerSettingsForm from 'components/CustomerSettingsForm';
import CustomerProfileForm from 'components/CustomerProfileForm';
import CustomerAddressesEditPage from './CustomerAddressesEditPage';

const EditCustomerForm = () => {
    const {id} = useParams();
    const navigate = useNavigate();

    const handleOpenCustomerPage = () => {
        navigate(`/customers/detail/${id}`);
    }

    const tabs = [
        {
          key: 'profile',
          label: (
            <span className='tabLabel'>
              <UserOutlined />
              Profile
            </span>
          ),
          children: (<CustomerProfileForm customerId={id} />),
        },
        {
          key: 'addresses',
          label: (
            <span className='tabLabel'>
              <HomeOutlined />
              Addresses
            </span>
          ),
          children: (<CustomerAddressesEditPage customerId={id} />),
        },
        {
          key: 'settings',
          label: (
            <span className='tabLabel'>
              <SettingOutlined />
              Settings
            </span>
          ),
          children: (<CustomerSettingsForm customerId={id} />),
        }
        
      ];

    return (
        <>
            <MainCard title={`Edit Customer #${id}`}>
                <Row>
                    <Col span={22} offset={1} style={{textAlign: 'right'}}>
                        <Button onClick={handleOpenCustomerPage} type="primary">
                            Customer page
                        </Button>
                    </Col>
                    <Col span={22} offset={1}>
                        <Tabs defaultActiveKey="profile" items={tabs} />
                    </Col>
                </Row>
            </MainCard>
        </>
    )
};

export default EditCustomerForm;