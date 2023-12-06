import React, { useState } from 'react';
import { Button, message, Steps, theme } from 'antd';
import MainCard from 'components/MainCard';
import CustomerProfileForm from 'components/CustomerProfileForm';
import { Navigate, useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import CustomerSettingsForm from 'components/CustomerSettingsForm';
import CustomerAddressForm from 'components/CustomerAddressForm';
import { Checkbox, Col, Row } from '../../../../node_modules/antd/es/index';

const CustomerForm = () => {
    const navigate = useNavigate();
    const [current, setCurrent] = useState(0);
    const [customerId, setCustomerId] = useState(null);
    const [customerEmail, setCustomerEmail] = useState(null);
    const [phoneNumber, setPhoneNumber] = useState(null);
    const [userAddresses, setUserAddresses] = useState(0);
    const [addBillingAddress, setAddBillingAddress] = useState(false);

    const next = () => {
        setCurrent(current + 1);
    };

    const handleSuccessFirstStep = (customer) => {
        setCustomerId(customer.id);
        setCustomerEmail(customer.email);
        setPhoneNumber(customer.phoneNumber);
        setCurrent(current + 1);
    }

    const handleSuccessLastStep = () => {
        navigate(`/customers/detail/${customerId}`);
    }

    const handleSuccessAddressForm = () => {
        setUserAddresses(userAddresses + 1);
    }

    const steps = [
        {
            title: 'Requaired Informations',
            content: (<CustomerProfileForm onSuccessSaveFunction={handleSuccessFirstStep}/>),
        },
        {
            title: 'Address',
            content: (
                <>
                    <Row>
                        <Col span={24}>
                            <CustomerAddressForm
                                customerId={customerId}
                                onSuccessSaveFunction={handleSuccessAddressForm} 
                                type={1}
                                email={customerEmail}
                                phoneNumber={phoneNumber}
                            />
                        </Col>
                    </Row>
                    <Row style={{textAlign: 'left', height: 'auto'}}>
                        <Col span={22} offset={1} >
                            <Checkbox
                                style={{marginTop: '10px', marginBottom: '10px'}}
                                checked={addBillingAddress} 
                                onChange={(value) => {setAddBillingAddress(value.target.checked);}}
                            >
                                Use different address as billing address
                            </Checkbox>
                        </Col>
                    </Row>
                    { addBillingAddress ? 
                    <Row>
                        <Col span={24}>
                            <CustomerAddressForm 
                                customerId={customerId}
                                onSuccessSaveFunction={handleSuccessAddressForm} 
                                type={0}
                                email={customerEmail}
                                phoneNumber={phoneNumber}
                            />
                        </Col>
                    </Row> : null }
                    { userAddresses > 0 ?
                    <Row style={{textAlign: 'right'}}>
                        <Col span={22} offset={1}>
                            <Button type="primary" onClick={next}>
                                Next
                            </Button>
                        </Col>
                    </Row> : null }
                </>
            ),
        },
        {
            title: 'Settings & Consents',
            content: (<CustomerSettingsForm customerId={customerId} onSuccessSaveFunction={handleSuccessLastStep} />),
        },
    ];

    const items = steps.map((item) => ({
        key: item.title,
        title: item.title,
    }));
    const contentStyle = {
        marginTop: 16,
    };
    return (
        <>
            <MainCard title="Add Customer">
                <Steps current={current} items={items} />
                <div style={contentStyle}>{steps[current].content}</div>
            </MainCard>
        </>
    )
};

export default CustomerForm;