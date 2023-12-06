import { useEffect, useState } from 'react';
import { Button, Col, DatePicker, Form, Input, Row, Spin, notification } from '../../node_modules/antd/es/index';
import './SharedStyles.css'
import instance from 'utils/api';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'


const CustomerProfileForm = ({onSuccessSaveFunction, customerId}) => {
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState({
        firstName: "",
        secondName: "",
        lastName: "",
        birthDate: "",
        socialSecurityNumber: "",
        phoneNumber: "",
        email: "",
        password: ""
    });

    const fetchCustomerData = async () => {
        try {
            if (customerId != null || customerId != undefined) {
                setLoading(true);
                dayjs.extend(utc);
                const response = await instance.get(`/customers/${customerId}/detail`);

                if (response.status != 200) {
                    setLoading(false);
                    notification.error({
                        message: "Can't fetch user data.",
                        type: "error",
                        placement: "bottomRight"
                    });

                    return;
                }

                setFormData({
                    firstName: response.data.customer.firstName,
                    secondName: response.data.customer.secondName,
                    lastName: response.data.customer.lastName,
                    birthDate: dayjs.utc(response.data.customer.birthDate.date).local(),
                    socialSecurityNumber: response.data.customer.socialSecurityNumber,
                    phoneNumber: response.data.customer.phoneNumber,
                    email: response.data.customer.email,
                });

                setLoading(false);
            }
        } catch (e) {
            setLoading(false);
            notification.error({
                message: "Can't fetch user detail.",
                description: e.message,
                type: "error",
                placement: "bottomRight"
            })
        } finally {
            setLoading(false);
        }
    }

    const generateRandomPassword = (length = 12) => {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let password = '';
        
        for (let i = 0; i < length; i++) {
          const randomIndex = Math.floor(Math.random() * charset.length);
          password += charset[randomIndex];
        }
      
        return password;
    };

    const saveCustomer = async () => {
        try {
            setLoading(true);

            const response = await instance.patch(`/customers/${customerId}/edit`, {
                firstName: formData.firstName,
                lastName: formData.lastName,
                secondName: formData.secondName,
                birthDate: formData.birthDate,
                phoneNumber: formData.phoneNumber,
                email: formData.email
            });

            if (response.status != 200) {
                setLoading(false);

                notification.error({
                    message: "Can't save customer profile.",
                    type: "error",
                    placement: "bottomRight"
                });

                return;
            }

            setLoading(false);
            notification.success({
                message: "Successfully updated customer profile.",
                type: "success",
                placement: "bottomRight"
            })
        } catch (e) {
            setLoading(false);
            notification.error({
                message: "Can't save customer profile.",
                placement: "bottomRight",
                type: "error",
                description: e.message
            });
        } finally {
            setLoading(false);
        }
    }

    const addCustomer = async () => {
        try {
            setLoading(true);
            const response = await instance.post(`/customers/add`, {
                password: generateRandomPassword(),
                firstName: formData.firstName,
                secondName: formData.secondName,
                lastName: formData.lastName,
                birthDate: formData.birthDate,
                socialSecurityNumber: formData.socialSecurityNumber,
                phoneNumber: formData.phoneNumber,
                email: formData.email
            });

            if (response.status != 200) {
                setLoading(false);

                notification.error({
                    message: "Can't create customer.",
                    type: "error",
                    placement: "bottomRight"
                });

                return;
            }

            setLoading(false);
            notification.success({
                message: "Successfully created customer entity.",
                type: "success",
                placement: "bottomRight"
            });
            onSuccessSaveFunction(response.data.customer);
        } catch (e) {
            setLoading(false);
            notification.error({
                message: "Can't create customer.",
                description: e.message,
                type: "error",
                placement: "bottomRight"
            });
        } finally {
            setLoading(false);
        }
    }

    const handleInputChange = (event) => {
        const { name, value } = event.target;
        setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
      };

    useEffect(() => {
        fetchCustomerData();
    }, [customerId])

    return (
        <>
        <Spin spinning={loading}>
            <Form layout="vertical" style={{ width: 100 + "%" }}>
                <Row>
                    <Col span={10} offset={1}>
                        <Form.Item 
                            label="First Name"
                            required
                            tooltip="This is a required field"
                        >
                            <Input placeholder="John" name="firstName" value={formData.firstName} onChange={handleInputChange} />
                        </Form.Item>
                    </Col>
                    <Col span={10} offset={2}>
                        <Form.Item 
                            label="Surname"
                            required
                            tooltip="This is a required field"
                        >
                            <Input placeholder="Doe" name="lastName" value={formData.lastName} onChange={handleInputChange} />
                        </Form.Item>
                    </Col>
                </Row>
                <Row>
                    <Col span={10} offset={1}>
                        <Form.Item label="Second Name">
                            <Input placeholder="Doe John" name="secondName" value={formData.secondName} onChange={handleInputChange} />
                        </Form.Item>
                    </Col>
                    <Col span={10} offset={2}>
                        <Form.Item 
                            label="Social Security number"
                            required
                            tooltip="This is a required field"
                        >
                            <Input
                                count={{
                                    show: true,
                                    max: 10,
                                }}
                                disabled={customerId ? true : false}
                                placeholder="Please insert social security number" 
                                name="socialSecurityNumber" 
                                value={formData.socialSecurityNumber} onChange={handleInputChange} 
                            />
                        </Form.Item>
                    </Col>
                </Row>
                <Row>
                    <Col span={10} offset={1}>
                        <Form.Item label="Birth Date">
                            <DatePicker style={{width: '100%'}} name="birthDate" value={formData.birthDate} onChange={(value) => handleInputChange({ target: { name: "birthDate", value } })} />
                        </Form.Item>
                    </Col>
                    <Col span={10} offset={2}>
                        <Form.Item 
                            label="Phone number"
                            required
                            tooltip="This is a required field"
                        >
                            <Input placeholder="+48 - - -" name="phoneNumber" value={formData.phoneNumber} onChange={handleInputChange} />
                        </Form.Item>
                    </Col>
                </Row>
                <Row>
                    <Col span={10} offset={1}>
                        <Form.Item 
                            label="Email"
                            required
                            tooltip="This is a required field"
                            rules={[
                                {
                                  type: "email",
                                  message: "Please enter a valid email address",
                                },
                            ]}
                        >
                            <Input placeholder="john.doe@email.com" name="email" value={formData.email} onChange={handleInputChange} />
                        </Form.Item>
                    </Col>
                </Row>
                <Row style={{textAlign: 'right'}}>
                    <Col span={22} offset={1}>
                        <Button type="primary" onClick={ customerId ? saveCustomer : addCustomer }>
                            { customerId ? 'Save' : 'Next' }
                        </Button>
                    </Col>
                </Row>
            </Form>
        </Spin>
        </>
    );
};

export default CustomerProfileForm;
