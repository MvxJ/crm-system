import React, { useState } from 'react';
import { Button, message, Steps, theme } from 'antd';
import MainCard from 'components/MainCard';
import CustomerProfileForm from 'components/CustomerProfileForm';
import { Navigate, useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import CustomerSettingsForm from 'components/CustomerSettingsForm';

const CustomerForm = () => {
    const navigate = useNavigate();
    const [current, setCurrent] = useState(2);
    const [customerId, setCustomerId] = useState(45);

    const handleSuccessFirstStep = (customer) => {
        setCustomerId(customer.id);
        setCurrent(current + 1);
    }

    const handleSuccessLastStep = () => {
        navigate(`/customers/detail/${customerId}`);
    }

    const steps = [
        {
            title: 'Requaired Informations',
            content: (<CustomerProfileForm onSuccessSaveFunction={handleSuccessFirstStep}/>),
        },
        {
            title: 'Address',
            content: 'Second-content',
        },
        {
            title: 'Settings & Consents',
            content: (<CustomerSettingsForm customerId={customerId} onSuccessSaveFunction={handleSuccessLastStep} />),
        },
    ];

    const next = () => {
        setCurrent(current + 1);
    };
    const prev = () => {
        setCurrent(current - 1);
    };
    const items = steps.map((item) => ({
        key: item.title,
        title: item.title,
    }));
    const contentStyle = {
        lineHeight: '260px',
        textAlign: 'center',
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