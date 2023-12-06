import React, { useEffect, useState } from 'react';
import MainCard from 'components/MainCard';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import './Customers.css';
import { SettingOutlined, HomeOutlined, UserOutlined } from '@ant-design/icons';
import CustomerSettingsForm from 'components/CustomerSettingsForm';
import CustomerProfileForm from 'components/CustomerProfileForm';
import instance from 'utils/api';
import CustomerAddressForm from 'components/CustomerAddressForm';

const CustomerAddressesEditPage = ({customerId}) => {
    const [loading, setLoading] = useState(false);
    const [addresses, setAddresses] = useState([]);
    const [addAddress, setAddAddress] = useState(false);

    const handleCloseFormAndReloadResults = () => {
        fetchData();
        setAddAddress(false);
    }

    const handleDeleteSuccess = () => {
        fetchData();
    }

    const fetchData = async () => {
        try {
            setLoading(true);

            const response = await instance.get(`/customers/address/list?customerId=${customerId}`);

            if (response.status != 200) {
                setLoading(false);
                
                notification.error({
                    type: "error",
                    message: "Can't fetch customer addresses.",
                    placement: "bottomRight"
                });

                return;
            }

            setAddresses(response.data.addresses);
            setLoading(false);
        } catch (e) {
            setLoading(false);
            notification.error({
                type: "error",
                message: "Can't fetch customer addresses.",
                placement: "bottomRight",
                descripotion: e.message
            });
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        fetchData();
    }, [])

    return (
        <>
            <Row style={{textAlign: 'right'}}>
                <Col span={22} offset={1}>
                    <Button type="primary" onClick={() => setAddAddress(!addAddress)}>{ addAddress ? 'Cancel' : 'New Address' }</Button>
                </Col>
            </Row>
            {addAddress ?
                <CustomerAddressForm customerId={customerId} onSuccessSaveFunction={handleCloseFormAndReloadResults}/> 
            : null}
            {addresses.map((address) => (
                <CustomerAddressForm address={address} customerId={customerId} onSuccessSaveFunction={handleDeleteSuccess} />
            ))}
        </>
    )
};

export default CustomerAddressesEditPage;