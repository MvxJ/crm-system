import React from 'react';
import { Typography, Paper, Grid } from '@mui/material';
import { Col } from '../../node_modules/antd/es/index';

const AddressInfo = ({ addresses, type }) => {
    const address = addresses[type] || addresses.contact;

    return (
        <div>
            {type === 'billing' ? <b>Billing Address</b> : <b>Contact Address</b>}
            <Grid container spacing={2}>
                <Grid item xs={12}>
                    <Col span={24}>
                        {`Country: ${address?.country}`}
                    </Col>
                    <Col span={24}>
                        {`Phone Number: ${address?.phoneNumber}`}
                    </Col>
                    <Col span={24}>
                        {`City: ${address?.city}`}
                    </Col>

                    <Col span={24}>
                        {`Zip Code: ${address?.zipCode}`}
                    </Col>

                    <Col span={24}>
                        {`Address: ${address?.address}`}
                    </Col>

                    {type === 'billing' && (
                        <>
                            <Col span={24}>
                                {`Company Name: ${address?.companyName || 'N/A'}`}
                            </Col>

                            <Col span={24}>
                                {`Tax ID: ${address?.taxId || 'N/A'}`}
                            </Col>
                        </>
                    )}
                </Grid>

            </Grid>
        </div>
    );
};

export default AddressInfo;





