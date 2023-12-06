import { useEffect, useState } from 'react';
import { Button, Col, Form, Row, Select, Spin, Switch, notification } from '../../node_modules/antd/es/index';
import './SharedStyles.css'
import instance from 'utils/api';

const CustomerSettingsForm = ({ customerId, onSuccessSaveFunction }) => {
    const [loading, setLoading] = useState(false);
    const [addresses, setAddresses] = useState([]);
    const [formData, setFormData] = useState({
        smsNotifications: false,
        emailNotifications: false,
        twoFactorAuth: false,
        contactAddress: null,
        billingAddress: null
    });

    const fetchCustomerSettings = async () => {
        try {
            setLoading(true);

            const response = await instance.get(`/customers/settings/${customerId}/detail`);
            const userAddresses = await instance.get(`/customers/address/list?customerId=${customerId}`);

            if (response.status != 200) {
                setLoading(false);
                notification.error({
                    message: "Can't fetch customer settings.",
                    type: "error",
                    placement: "bottomRight"
                })
                return;
            }

            if (userAddresses.data.addresses) {
                setAddresses(userAddresses.data.addresses)
            } else {
                setAddresses([])
            }

            setFormData({
                smsNotifications: response.data.settings.smsNotification,
                emailNotifications: response.data.settings.emailNotification,
                twoFactorAuth: response.data.settings.twoFactor,
                contactAddress: response.data.settings.contactAddressId,
                billingAddress: response.data.settings.billingAddressId
            });

            setLoading(false);
        } catch (e) {
            setLoading(false);
            notification.error({
                message: "Can't fetch customer settings.",
                type: "error",
                description: e.message,
                placement: "bottomRight"
            })
        } finally {
            setLoading(false)
        }
    };

    const saveCustomerSettings = async () => {
        try {
            setLoading(true);

            var request = {
                smsNotifications: formData.smsNotifications,
                emailNotifications: formData.emailNotifications,
                '2fa': formData.twoFactorAuth
            }

            if (formData.contactAddress != null) {
                request.contactAddress = formData.contactAddress;
            }

            if (formData.billingAddress != null) {
                request.billingAddress = formData.billingAddress;
            }

            const response = await instance.put(`/customers/settings/${customerId}/edit`, request);

            if (response.status != 200) {
                setLoading(false);

                notification.error({
                    message: "Can't save customer settings.",
                    placement: "bottomRight",
                    type: "error"
                });

                return;
            }

            setLoading(false);
            notification.success({
                message: "Successfully saved user settings.",
                type: "success",
                placement: "bottomRight"
            })
            if (onSuccessSaveFunction) {
                onSuccessSaveFunction();
            }
        } catch (e) {
            setLoading(false);
            notification.error({
                message: "Can't save customer settings.",
                description: e.message,
                placement: "bottomRight",
                type: "error"
            })
        } finally {
            setLoading(false);
        }
    }

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
    };

    useEffect(() => {
        fetchCustomerSettings()
    }, [customerId]);

    return (
        <>
            <Spin spinning={loading}>
                <Form layout="vertical" style={{ width: 100 + '%' }}>
                    <Row>
                        <Col span={10} offset={1}>
                            <Form.Item label="Contact Address">
                                <Select 
                                    style={{textAlign: 'left'}} 
                                    value={formData.contactAddress}
                                    onChange={(value) => {
                                        setFormData((prevFormData) => ({ ...prevFormData, contactAddress: value }));
                                    }}
                                >
                                    {addresses.map((address) => (
                                        <Select.Option key={address.id} value={address.id}>
                                            {address.city}, {address.zipCode}, {address.address}, ({address.country}) {address.companyName ? '(' + address.companyName : null} {address.taxId ? address.taxId + ')' : null}
                                        </Select.Option>
                                    ))}
                                </Select>
                            </Form.Item>
                        </Col>
                        <Col span={10} offset={2}>
                            <Form.Item label="Billing Address">
                                <Select 
                                    style={{textAlign: 'left'}} 
                                    value={formData.billingAddress} 
                                    onChange={(value) => {
                                        setFormData((prevFormData) => ({ ...prevFormData, billingAddress: value }));
                                    }}
                                >
                                    {addresses.map((address) => (
                                        <Select.Option key={address.id} value={address.id}>
                                            {address.city}, {address.zipCode}, {address.address}, ({address.country}) {address.companyName ? '(' + address.companyName : null} {address.taxId ? address.taxId + ')' : null}
                                        </Select.Option>
                                    ))}
                                </Select>
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row>
                        <Col span={6} offset={1}>
                            <Form.Item label="2fa Authentication">
                                <Switch
                                    name="twoFactorAuth"
                                    checked={formData.twoFactorAuth}
                                    onChange={(value) => handleInputChange({ target: { name: "twoFactorAuth", value } })}
                                />
                            </Form.Item>
                        </Col>
                        <Col span={6} offset={2}>
                            <Form.Item label="Sms Notifications">
                                <Switch
                                    name="smsNotifications"
                                    checked={formData.smsNotifications}
                                    onChange={(value) => handleInputChange({ target: { name: "smsNotifications", value } })}
                                />
                            </Form.Item>
                        </Col>
                        <Col span={6} offset={2}>
                            <Form.Item label="Email Notification">
                                <Switch
                                    name="emailNotifications"
                                    checked={formData.emailNotifications}
                                    onChange={(value) => handleInputChange({ target: { name: "emailNotifications", value } })}
                                />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row style={{ textAlign: 'right' }}>
                        <Col span={22} offset={1}>
                            <Button type="primary" onClick={saveCustomerSettings}>
                                { onSuccessSaveFunction ? 'Finish' : 'Save' }
                            </Button>
                        </Col>
                    </Row>
                </Form>
            </Spin>
        </>
    );
};

export default CustomerSettingsForm;
