// material-ui
import { Typography } from '@mui/material';
import React, { useEffect, useState } from 'react';
import { InfoCircleOutlined, UploadOutlined } from '@ant-design/icons';
import { PlusOutlined } from '@ant-design/icons';
import './Administration.css'
import {
  Button,
  DatePicker,
  Form,
  Input,
  Upload,
} from 'antd';

const { RangePicker } = DatePicker;
const { TextArea } = Input;

const normFile = (e) => {
  if (Array.isArray(e)) {
    return e;
  }
  return e?.fileList;
};

import MainCard from 'components/MainCard';
import { Col, Row, Spin, notification } from '../../../node_modules/antd/es/index';
import instance from 'utils/api';
import SettingsService from 'utils/Settings';

const SystemSettingsPage = () => {
  const [settings, setSettings] = useState([]);
  const [selectedFile, setSelectedFile] = useState(null);
  const [loading, setLoading] = useState(false);
  const [logoName, setLogoName] = useState(null);
  const [previewImage, setPreviewImage] = useState(null);
  const [formData, setFormData] = useState(
    {
      companyName: "",
      companyAddress: "",
      companyPhoneNumber: "",
      emailAddress: "",
      facebookUrl: "",
      privacyPolicy: "",
      termsAndConditions: "",
      mailerAddress: "",
      technicalSupportNumber: "",
      mailerName: ""
    }
  );

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await instance.get(`/settings/`);
      setSettings(response.data.settings);
      setFormData({
        companyName: response.data.settings.companyName,
        companyAddress: response.data.settings.companyAddress,
        companyPhoneNumber: response.data.settings.companyPhoneNumber,
        emailAddress: response.data.settings.emailAddress,
        facebookUrl: response.data.settings.facebookUrl,
        privacyPolicy: response.data.settings.privacyPolicy,
        termsAndConditions: response.data.settings.termsAndConditions,
        mailerAddress: response.data.settings.mailerAddress,
        technicalSupportNumber: response.data.settings.technicalSupportNumber,
        mailerName: response.data.settings.mailerName
      });
      setLogoName(response.data.settings.logoUrl);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch system settings",
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  const saveSettings = async () => {
    try {
      setLoading(true);
      await SettingsService.updateSettings(settings.id, {
        companyName: formData.companyName,
        companyAddress: formData.companyAddress,
        companyPhoneNumber: formData.companyPhoneNumber,
        emailAddress: formData.emailAddress,
        facebookUrl: formData.facebookUrl,
        privacyPolicy: formData.privacyPolicy,
        termsAndConditions: formData.termsAndConditions,
        mailerAddress: formData.mailerAddress,
        mailerName: formData.mailerName,
        technicalSupportNumber: formData.technicalSupportNumber
      });

      setLoading(false);
      notification.success({
        message: "Saved!",
        type: 'success',
        placement: 'bottomRight'
      });
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't save settings!",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const uploadLogo = async (file) => {
    try {
      await SettingsService.uploadLogo(settings.id, file);
      notification.success({
        message: "Successfully uploaded logo file.",
        type: 'success',
        placement: 'bottomRight'
      });
      fetchData();
    } catch (e) {
      notification.error({
        message: "Can't upload logo file.",
        type: 'error',
        placement: 'bottomRight'
      });
    }
  }

  useEffect(() => {
    fetchData();
  }, []
  );

  return (
    <>
      <Spin spinning={loading}>
        <MainCard title="Settings">
          <Form
            layout="vertical"
            style={{ width: 100 + '%' }}
          >
            <Row style={{ textAlign: 'right' }}>
              <Col span={22} offset={1}>
                <Form.Item>
                  <Button type="primary" size="middle" onClick={saveSettings}>Save</Button>
                </Form.Item>
              </Col>
            </Row>
            <Row><Col span={22} offset={1}>
              <h4>Basic Settings</h4>
            </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Company Name" required tooltip="This is a required field">
                  <Input placeholder="Company name..." value={formData.companyName} onChange={handleInputChange} name="companyName" />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Company Address"
                  required
                  tooltip="This is a required field"
                >
                  <Input placeholder="Company address..." value={formData.companyAddress} onChange={handleInputChange} name="companyAddress" />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Email address" required tooltip="This is a required field">
                  <Input placeholder="Email address..." value={formData.emailAddress} onChange={handleInputChange} name="emailAddress" />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Technical Support number"
                  required
                  tooltip="This is a required field"
                >
                  <Input placeholder="Technical support number..." value={formData.technicalSupportNumber} onChange={handleInputChange} name="technicalSupportNumber" />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Company Number" required tooltip="This is a required field">
                  <Input placeholder="Company number..." value={formData.companyPhoneNumber} onChange={handleInputChange} name="companyPhoneNumber" />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={22} offset={1}>
                <h4>Mailer Settings</h4>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Mailer Address" required tooltip="This is a required field">
                  <Input placeholder="Mailer address..." value={formData.mailerAddress} onChange={handleInputChange} name="mailerAddress" />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Mailer Name"
                  required
                  tooltip="This is a required field"
                >
                  <Input placeholder="Mailer name..." value={formData.mailerName} onChange={handleInputChange} name="mailerName" />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={22} offset={1}>
                <h4>Additional Settings</h4>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Terms and Conditions">
                  <TextArea
                    showCount
                    maxLength={500}
                    style={{ height: 120, resize: 'none' }}
                    placeholder="Terms and conditions..."
                    value={formData.termsAndConditions}
                    onChange={handleInputChange}
                    name="termsAndConditions"
                  />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item label="Privacy and Policy">
                  <TextArea
                    showCount
                    maxLength={500}
                    style={{ height: 120, resize: 'none' }}
                    placeholder="Privacy and policy..."
                    value={formData.privacyPolicy}
                    onChange={handleInputChange}
                    name="privacyPolicy"
                  />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Company logo">
                  <Upload
                    action={(file) => { uploadLogo(file) }}
                    listType="picture"
                    maxCount={1}
                    className="uploadContainer"
                  >
                    {previewImage || logoName ? (
                      <div>
                        <img
                          src={previewImage ? previewImage : `http://localhost:8000/api/file/display/${logoName}`}
                          alt="system_logo"
                          style={{ maxWidth: '100px', maxHeight: '100px' }}
                        />
                      </div>
                    ) : null}
                    <Button icon={<UploadOutlined />}>Upload (Max: 1)</Button>
                  </Upload>
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item label="Facebook Url">
                  <Input addonBefore="http://" defaultValue="facebook.com" value={formData.facebookUrl} onChange={handleInputChange} name="facebookUrl" />
                </Form.Item>
              </Col>
            </Row>
          </Form>
        </MainCard>
      </Spin>
    </>);
};

export default SystemSettingsPage;
