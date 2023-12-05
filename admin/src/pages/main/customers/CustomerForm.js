import React, { useState } from 'react';
import { Button, message, Steps, theme } from 'antd';
import MainCard from 'components/MainCard';

const CustomerForm = () => {
    const steps = [
        {
            title: 'Requaired Informations',
            content: 'Requaired informations',
        },
        {
            title: 'Address',
            content: 'Second-content',
        },
        {
            title: 'Settings & Consents',
            content: 'Last-content',
        },
    ];

    const [current, setCurrent] = useState(0);
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
                <div
                    style={{
                        marginTop: 24,
                    }}
                >
                    {current < steps.length - 1 && (
                        <Button type="primary" onClick={() => next()}>
                            Next
                        </Button>
                    )}
                    {current === steps.length - 1 && (
                        <Button type="primary" onClick={() => message.success('Processing complete!')}>
                            Done
                        </Button>
                    )}
                    {current > 0 && (
                        <Button
                            style={{
                                margin: '0 8px',
                            }}
                            onClick={() => prev()}
                        >
                            Previous
                        </Button>
                    )}
                </div>
            </MainCard>
        </>
    )
};

export default CustomerForm;