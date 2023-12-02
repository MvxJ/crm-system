import React, { useState } from 'react';
import MainCard from 'components/MainCard';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import './Visit.css';
import { PlusOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { Button, Col, DatePicker, Form, Input, Modal, Row, Switch, TimePicker } from '../../../../node_modules/antd/es/index';
import DebounceSelect from 'utils/DebounceSelect';
import instance from 'utils/api';

const VisitsList = () => {
  const [currentView, setCurrentView] = useState('timeGridWeek');
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const { TextArea } = Input;

  const toolBar = {
    start: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    center: 'title',
    end: 'prev,next',
  }

  async function searchCustomers(inputValue) {
    try {
      const response = await instance.get(`/customers/list?searchTerm=${inputValue}`);

      if (response.data.results.customers) {
        return response.data.results.customers.map((customer) => ({
          label: `#${customer.id} ${customer.firstName} ${customer.lastName} (${customer.socialSecurityNumber ? customer.socialSecurityNumber : 'empty'})`,
          value: customer.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  async function searchUser(inputValue) {
    try {
      const response = await instance.get(`/users/list?searchTerm=${inputValue}`);

      if (response.data.results.users) {
        return response.data.results.users.map((user) => ({
          label: `#${user.id} ${user.name} ${user.surname} (${user.username})`,
          value: user.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  const showAddModal = () => {
    setIsModalVisible(true);
  };

  const handleCancel = () => {
    setIsModalVisible(false);
  };

  const handleDateClick = (info) => {
    console.log('Date clicked', info.date);
    setIsModalVisible(true);
  }

  return (
    <>
      <MainCard title="Service Visits" style={{ maxHeight: '85vh', overflowY: 'scroll' }}>
        <Row style={{ marginBottom: '15px' }}>
          <Col span={10} offset={14} style={{ textAlign: 'right' }}>
            <DebounceSelect
              mode="single"
              value={selectedUser}
              placeholder="Search user..."
              onChange={(value) => {
                setSelectedUser(value);
              }}
              fetchOptions={searchUser}
              style={{
                width: '100%',
              }}
            />
          </Col>
        </Row>
        <FullCalendar
          plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin]}
          initialView={currentView}
          headerToolbar={toolBar}
          nowIndicator={true}
          view={currentView}
          events={[
            { title: 'Event 1', date: '2023-12-01' },
            { title: 'Event 2', date: '2023-12-05' },
          ]}
          editable={true}
          dateClick={handleDateClick}
        />
        <Modal title="Add Service Visit" visible={isModalVisible} onCancel={handleCancel} footer={null}>
          <Form>
            <Form.Item label="Title" name="title" rules={[{ required: true, message: 'Please enter a title!' }]}>
              <Input />
            </Form.Item>
            <Form.Item label="Event Date">
              <DatePicker />
            </Form.Item>
            <Form.Item label="Start Time" name="startTime" rules={[{ required: true, message: 'Please select a start time!' }]}>
              <TimePicker format="HH:mm" />
            </Form.Item>
            <Form.Item label="End Time" name="startTime" rules={[{ required: true, message: 'Please select a start time!' }]}>
              <TimePicker format="HH:mm" />
            </Form.Item>
            <Form.Item>
              <DebounceSelect
                mode="single"
                value={selectedUser}
                placeholder="Search user..."
                onChange={(value) => {
                  setSelectedUser(value);
                }}
                fetchOptions={searchUser}
                style={{
                  width: '100%',
                }}
              />
            </Form.Item>
            <Form.Item>
              <TextArea
                showCount
                maxLength={500}
                style={{ resize: 'none' }}
                placeholder="Privaolicy...cy and p"
                name="privacyPolicy"
              />
            </Form.Item>
            <Form.Item label="Finished">
              <Switch
                checkedChildren={<CheckOutlined />}
                unCheckedChildren={<CloseOutlined />}
                defaultChecked
              />
            </Form.Item>
            <Form.Item label="Cancelled">
              <Switch
                checkedChildren={<CheckOutlined />}
                unCheckedChildren={<CloseOutlined />}
                defaultChecked
              />
            </Form.Item>
            <Form.Item>
              <Form.Item label="Customer">
                <DebounceSelect
                  mode="single"
                  placeholder="Search customer..."
                  fetchOptions={searchCustomers}
                  style={{
                    width: '100%',
                  }}
                />
              </Form.Item>
              <Button type="primary" htmlType="submit">
                Submit
              </Button>
            </Form.Item>
          </Form>
        </Modal>
        <Button
          type="primary"
          style={{ fontSize: '28px', position: 'absolute', right: '50px', bottom: '50px', zIndex: '1000', borderRadius: '50%', height: '55px', width: '55px', display: 'flex', justifyContent: 'center', alignItems: 'center' }}
          onClick={showAddModal}
        >
          <PlusOutlined />
        </Button>
      </MainCard>
    </>
  );
};

export default VisitsList;