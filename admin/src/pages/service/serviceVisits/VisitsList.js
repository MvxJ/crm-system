import React, { useEffect, useState } from 'react';
import MainCard from 'components/MainCard';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import './Visit.css';
import { PlusOutlined, CheckOutlined, CloseOutlined, SelectOutlined } from '@ant-design/icons';
import { Button, Col, ColorPicker, DatePicker, Form, Input, Modal, Row, Spin, Switch, TimePicker, notification } from 'antd';
import DebounceSelect from 'utils/DebounceSelect';
import instance from 'utils/api';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';
import { Select } from '../../../../node_modules/antd/es/index';

const VisitsList = () => {
  const navigate = useNavigate();
  const [currentView, setCurrentView] = useState('timeGridWeek');
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [selectedTechnician, setSelectedTechnician] = useState(null)
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [selectedServiceRequest, setSelectedServiceRequest] = useState(null);
  const [loading, setLoading] = useState(false);
  const [formLoading, setFormLoading] = useState(false);
  const [selectedEvent, setSelectedEvent] = useState(null);
  const { TextArea } = Input;
  const [startTime, setStartTime] = useState('');
  const [endTime, setEndTime] = useState('');
  const [serviceRequests, setServiceRequests] = useState([]);
  const [serviceVisits, setServiceVisits] = useState([])
  const [formData, setFormData] = useState({
    startTime: '',
    endTime: '',
    date: null,
    cancelled: '',
    finished: '',
    title: '',
    description: '',
    customer: '',
    user: '',
    contract: '',
    color: "#91d5ff"
  });

  const toolBar = {
    start: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    center: 'title',
    end: 'prev,next',
  }

  const fetchVisitDetail = async (visitId, event, useEventTime = false) => {
    try {
      dayjs.extend(utc);
      setFormLoading(true);

      const response = await instance.get(`/service/visit/${visitId}/detail`);

      if (response.status != 200) {
        setFormLoading(false);

        notification.error({
          message: "Can't fetch visit details.",
          type: "error",
          placement: "bottomRight"
        })

        return;
      }

      var start = dayjs.utc(response.data.serviceVisit.start.date).local().format('HH:mm');
      var end = dayjs.utc(response.data.serviceVisit.end.date).local().format('HH:mm');
      var date = dayjs.utc(response.data.serviceVisit.date.date).local();

      if (useEventTime) {
        const startDate = dayjs.utc(event.startStr).local();
        start = startDate.format('HH:mm');
        end = dayjs.utc(event.endStr).local().format('HH:mm');;
        date = startDate;
      }

      setFormData({
        startTime: start,
        endTime: end,
        date: date,
        cancelled: response.data.serviceVisit.cancelled,
        finished: response.data.serviceVisit.finished,
        title: response.data.serviceVisit.title,
        description: response.data.serviceVisit.description,
        color: response.data.serviceVisit.color,
      });
      

      if (response.data.serviceVisit.user) {
        setSelectedUser({
          label: `#${response.data.serviceVisit.user.id} ${response.data.serviceVisit.user.name} ${response.data.serviceVisit.user.surname} (${response.data.serviceVisit.user.username})`,
          value: response.data.serviceVisit.user.id,
        });
      }
        
      setSelectedCustomer({
        label: `#${response.data.serviceVisit.customer.id} ${response.data.serviceVisit.customer.name} ${response.data.serviceVisit.customer.surname} (${response.data.serviceVisit.customer.email ? response.data.serviceVisit.customer.email : 'empty'})`,
        value: response.data.serviceVisit.customer.id,
      });
    } catch (e) {
      setFormLoading(false);

      notification.error({
        message: "Can't fetch visit details.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setFormLoading(false);
    }
  }

  const fetchData = async () => {
    try {
      setLoading(true);
      dayjs.extend(utc);

      var url = `/service/visit/list`;

      if (selectedTechnician) {
        url += `?userId=${selectedTechnician.value}`;
      }

      const response = await instance.get(url);

      if (response.status !== 200) {
        setLoading(false);

        notification.error({
          message: "Can't fetch service visits list.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      if (response.data.serviceVisits == null) {
        setServiceVisits([]);
        setLoading(false);
        return;
      }

      const events = response.data.serviceVisits.serviceVisits.map(serviceVisit => {
        const date = dayjs(serviceVisit.date.date).toDate();
        const startHour = dayjs(serviceVisit.start.date).toDate();
        const endHour = dayjs(serviceVisit.end.date).toDate();

        const startDateTime = new Date(date.getFullYear(), date.getMonth(), date.getDate(), startHour.getHours(), startHour.getMinutes());
        const endDateTime = new Date(date.getFullYear(), date.getMonth(), date.getDate(), endHour.getHours(), endHour.getMinutes());

        const visit = {
          id: serviceVisit.id,
          title: serviceVisit.title,
          start: startDateTime,
          end: endDateTime,
          color: serviceVisit.color
        };

        return visit;
      });

      setServiceVisits(events);
      setLoading(false);
    } catch (e) {
      setLoading(false);

      notification.error({
        message: "Can't fetch service visits list.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setLoading(false);
    }
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

  const format = 'HH:mm';

  const showAddModal = () => {
    setIsModalVisible(true);
  };

  const handleCancel = () => {
    setFormData({
    startTime: '',
    endTime: '',
    date: null,
    cancelled: '',
    finished: '',
    title: '',
    description: '',
    customer: '',
    user: '',
    contract: '',
    color: "#91d5ff"
  });
    setSelectedEvent(null);
    setSelectedCustomer(null);
    setSelectedServiceRequest(null);
    setServiceRequests([]);
    setIsModalVisible(false);
    setSelectedUser(null);
  };

  const handleNavigateCustomer = () => {
    navigate(`/customers/detail/${selectedCustomer.value}`);
  }

  const fetchCustomerServiceRequests = async () => {
    if (selectedCustomer) {
      try {
        const response = await instance.get(`/service-requests/list?customerId=${selectedCustomer.value}`);
        setServiceRequests(response.data.results.serviceRequests);
      } catch (e) {
        notification.error({
          message: "Can't fetch customer service requests.",
          description: e.message,
          placement: "bottomRight",
          type: "error"
        })
      } finally {
      }
    }
  }

  const handleEventResize = async (resizeInfo) => {
    const { event } = resizeInfo;
    await handleEventInteraction(event);
  };

  const handleEventDrop = async (dropInfo) => {
    const { event } = dropInfo;
    await handleEventInteraction(event);
  };

  const handleEventInteraction = async (event) => {
    try {
      const visitId = event.id;
      await fetchVisitDetail(visitId, event, true);

      setSelectedEvent(event);
      setIsModalVisible(true);
    } catch (error) {
      console.error("Error handling event interaction:", error);
    }
  };

  const handleEventClick = async (clickInfo) => {
    setSelectedEvent(clickInfo.event);
    setIsModalVisible(true)
    await fetchVisitDetail(clickInfo.event.id);
  };

  const createRequestObj = () => {
    const requestObj = {
      title: formData.title, 
      startTime: formData.startTime, 
      endTime: formData.endTime, 
      customer: selectedCustomer.value,
      date: formData.date,
      description: formData.description,
      color: typeof formData.color === 'string' ? formData.color : '#' + formData.color.toHex(),
      cancelled: Boolean(formData.cancelled),
      isFinished: Boolean(formData.finished)
    };

    if (selectedUser) {
      requestObj.user = selectedUser.value;
    }

    if (selectedServiceRequest) {
      requestObj.serviceRequest = selectedServiceRequest;
    }

    return requestObj;
  }

  const handleDateClick = (info) => {
    const clickedDate = dayjs(info.date);
    const startHour = clickedDate.format('HH:mm');
    const endHour = clickedDate.add(1, 'hour').format('HH:mm');

    setStartTime(startHour);
    setEndTime(endHour);

    setFormData({
      ...formData,
      date: clickedDate,
      startTime: startHour,
      endTime: endHour
    });

    setIsModalVisible(true);
  };

  const handleEventRender = (info) => {
    info.el.style.backgroundColor = info.event.ColorPicker;
  };

  const saveVisit = async () => {
    try {
      setFormLoading(true);
      const request = createRequestObj();
      const response = await instance.patch(`/service/visit/${selectedEvent.id}/edit`, request);

      if (response.status != 200) {
        setFormLoading(false);

        notification.error({
          message: "Can't update service visit.",
          type: "error",
          placement: "bottomRight"
        })

        return;
      }

      notification.success({
        message: "Successfully updated visit.",
        type: "success",
        placement: "bottomRight"
      });

      setFormLoading(false);
      handleCancel();
      fetchData();
    } catch (e) {
      setFormLoading(false);
      notification.error({
        message: "Can't update service visit.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setFormLoading(false);
    }
  }

  const addVisit = async () => {
    try {
      setFormLoading(true);
      const request = createRequestObj();
      const response = await instance.post(`/service/visit/add`, request);
      if (response.status != 200) {
        setFormLoading(false);

        notification.error({
          message: "Can't add service visit.",
          type: "error",
          placement: "bottomRight"
        })

        return;
      }

      notification.success({
        message: "Successfully added visit.",
        type: "success",
        placement: "bottomRight"
      });

      setFormLoading(false);
      handleCancel();
      fetchData();
    } catch (e) {
      setFormLoading(false);
      notification.error({
        message: "Can't add service visit.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setFormLoading(false);
    }
  }

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  useEffect(() => {
    fetchData();
  }, [selectedTechnician]);

  useEffect(() => {
    fetchCustomerServiceRequests();
  }, [selectedCustomer])

  return (
    <>
      <Spin spinning={loading}>
        <MainCard title="Service Visits" style={{ maxHeight: '85vh', overflowY: 'scroll' }}>
          <Row style={{ marginBottom: '15px' }}>
            <Col span={10} offset={14} style={{ textAlign: 'right' }}>
              <Form layout="vertical" style={{ width: 100 + "%" }}>
                <Form.Item label="Choose Technician">
                  <DebounceSelect
                    mode="single"
                    value={selectedTechnician}
                    placeholder="Search technician..."
                    onChange={(value) => {
                      setSelectedTechnician(value);
                    }}
                    fetchOptions={searchUser}
                    style={{
                      width: '100%',
                    }}
                  />
                </Form.Item>
              </Form>
            </Col>
          </Row>
          <FullCalendar
            plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin]}
            initialView={currentView}
            headerToolbar={toolBar}
            nowIndicator={true}
            view={currentView}
            events={serviceVisits}
            editable={true}
            dateClick={handleDateClick}
            eventClick={handleEventClick}
            eventRender={handleEventRender}
            eventResize={handleEventResize}
            eventDrop={handleEventDrop}
            businessHours={{
              daysOfWeek: [1, 2, 3, 4, 5, 6],
              startTime: '07:00',
              endTime: '21:00',
            }}
            slotMinTime="06:00"
            slotMaxTime="22:00"
          />
            <Modal title={selectedEvent ? `Service Visit #${selectedEvent.id}` : "Add Service Visit"} visible={isModalVisible} onCancel={handleCancel} footer={null}>
            <Spin spinning={formLoading}>
              <Form layout="vertical" style={{ width: 100 + "%" }}>
                <Row>
                  <Col span={24}>
                    <Form.Item label="Title" required tooltip="This is a required field">
                      <Input placeholder="Title..." value={formData.title} name="title" onChange={handleInputChange} />
                    </Form.Item>
                  </Col>
                </Row>
                <Row>
                  <Col span={24}>
                    <Form.Item label="Event Date" required tooltip="This is a required field">
                      <DatePicker
                        name="date"
                        value={formData.date} 
                        style={{width: '100%'}} 
                        onChange={handleInputChange} 
                      />
                    </Form.Item>
                  </Col>
                </Row>
                <Row>
                  <Col span={11}>
                    <Form.Item label="Start Time" required tooltip="This is a required field">
                      <TimePicker 
                        name="startTime"
                        value={formData.startTime ? dayjs(formData.startTime, format) : null} 
                        format={format} 
                        style={{width: '100%'}} 
                        onChange={handleInputChange} 
                      />
                    </Form.Item>
                  </Col>
                  <Col span={11} offset={2}>
                    <Form.Item label="End Time" required tooltip="This is a required field">
                      <TimePicker 
                        name="endTime" 
                        value={formData.endTime ? dayjs(formData.endTime, format) : null} 
                        format={format} 
                        style={{ width: '100%'}} 
                        onChange={handleInputChange}
                      />
                    </Form.Item>
                  </Col>
                </Row>
                  <Row>
                    <Col span={24}>
                      <Form.Item label="Customer" required tooltip="This is a required field">
                        <DebounceSelect
                          disabled={selectedEvent ? true : false}
                          mode="single"
                          value={selectedCustomer}
                          placeholder="Search customer..."
                          onChange={(value) => {
                            setSelectedCustomer(value);
                          }}
                          fetchOptions={searchCustomers}
                          style={{
                            width: '100%',
                          }}
                        />
                        { selectedEvent ?
                      <a onClick={handleNavigateCustomer} style={{marginTop: '5px', marginLeft: '5px'}}>
                        <SelectOutlined />&nbsp; Open customer page
                      </a> : null }
                      </Form.Item>
                    </Col>
                  </Row>
                <Row>
                  <Col span={24}>
                    <Form.Item label="Technician" required tooltip="This is a required field">
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
                  </Col>
                </Row>
                { !selectedEvent && selectedCustomer ?
                  <Row>
                    <Col span={24}>
                      <Form.Item label="Service Request" required tooltip="This is a required field">
                        <Select
                  mode="single"
                  disabled={selectedEvent ? true : false}
                  placeholder="Choose service request"
                  onChange={(value) => {
                    setSelectedServiceRequest(value);
                  }}
                  style={{
                    width: '100%',
                  }}
                >
                  {serviceRequests.map((serviceRequest) => (
                    <Select.Option key={serviceRequest.id} value={serviceRequest.id}>
                      [{FormatUtils.getServiceRequestStatusBadge(serviceRequest.status).text}] #{serviceRequest.id} - {serviceRequest.contract.number} ({FormatUtils.formatDateWithTime(serviceRequest.createdDate.date)})
                    </Select.Option>
                  ))}
                </Select>
                      </Form.Item>
                    </Col>
                  </Row> : null }
                <Row>
                  <Col span={24}>
                    <Form.Item label="Description">
                      <TextArea
                        showCount
                        value={formData.description}
                        onChange={handleInputChange}
                        maxLength={500}
                        style={{ resize: 'none', height: '200px' }}
                        placeholder="Description"
                        name="description"
                      />
                    </Form.Item>
                  </Col>
                </Row>
                <Row>
                  <Col span={6}>
                    <Form.Item label="Event Color">
                      <ColorPicker 
                        value={formData.color} 
                        name="color" 
                        onChange={(value) => handleInputChange({ target: { name: "color", value } })} 
                      />
                    </Form.Item>
                  </Col>
                  {selectedEvent ?
                    <Col span={18}>
                      <Row>
                        <Col span={6} offset={3}>
                          <Form.Item label="Finished">
                            <Switch
                              name="finished"
                              checked={formData.finished}
                              checkedChildren={<CheckOutlined />}
                              unCheckedChildren={<CloseOutlined />}
                              onChange={(value) => handleInputChange({ target: { name: "finished", value } })} 
                            />
                          </Form.Item>
                        </Col>
                        <Col span={6} offset={3}>
                          <Form.Item label="Cancelled">
                            <Switch
                              checked={formData.cancelled}
                              name="cancelled"
                              checkedChildren={<CheckOutlined />}
                              unCheckedChildren={<CloseOutlined />}
                              onChange={(value) => handleInputChange({ target: { name: "cancelled", value } })} 
                            />
                          </Form.Item>
                        </Col>
                      </Row>
                    </Col> : null}
                </Row>
                <Row style={{ textAlign: 'right' }}>
                  <Col span={24}>
                    {!selectedEvent ?
                      <Button type="primary" htmlType="submit" onClick={addVisit}>
                        Add Service Visit
                      </Button> :
                      <Button type="primary" htmlType="submit" onClick={saveVisit}>
                        Save Service Visit
                      </Button>}
                  </Col>
                </Row>
              </Form>
              </Spin>
            </Modal>
          <Button
            type="primary"
            style={{ fontSize: '28px', position: 'absolute', right: '50px', bottom: '50px', zIndex: '1000', borderRadius: '50%', height: '55px', width: '55px', display: 'flex', justifyContent: 'center', alignItems: 'center' }}
            onClick={showAddModal}
          >
            <PlusOutlined />
          </Button>
        </MainCard>
      </Spin>
    </>
  );
};

export default VisitsList;