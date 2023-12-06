import { useState } from 'react';
import { Button, Col, DatePicker, Form, Input, Row, Spin, notification } from '../../node_modules/antd/es/index';
import './SharedStyles.css'
import instance from 'utils/api';

const CustomerProfileForm = ({onSuccessSaveFunction}) => {
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

    const generateRandomPassword = (length = 12) => {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let password = '';
        
        for (let i = 0; i < length; i++) {
          const randomIndex = Math.floor(Math.random() * charset.length);
          password += charset[randomIndex];
        }
      
        return password;
    };

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
                            <DatePicker style={{width: '100%'}} name="birthDate" onChange={(value) => handleInputChange({ target: { name: "birthDate", value } })} />
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
                        <Button type="primary" onClick={addCustomer}>
                            Next
                        </Button>
                    </Col>
                </Row>
            </Form>
        </Spin>
        </>
    );
};

export default CustomerProfileForm;
